<?php

use App\Models\Page;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();

    $this->category = Page::factory()->create([
        'title' => 'Best Digital Airport Services Marketplace',
        'slug' => 'best-digital-airport-services-marketplace',
        'badge_image' => '/uploads/airport-online-winner-2026.png',
    ]);
});

function winnerFormData(Page $category, array $overrides = []): array
{
    return array_merge([
        'category_page_id' => $category->id,
        'company_name' => 'Airport.online',
        'won_at' => '2026-10-24',
        'status' => 1,
        'category_badge_image' => $category->badge_image,
    ], $overrides);
}

test('admin create form shows slug field', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.winners.create'))
        ->assertOk()
        ->assertSee('name="verification_slug"', false);
});

test('won_at is required', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.winners.store'), winnerFormData($this->category, ['won_at' => '']))
        ->assertSessionHasErrors('won_at');
});

test('store generates verification slug and avoids collisions', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('admin.winners.store'), winnerFormData($this->category))->assertSessionHasNoErrors();
    $this->actingAs($user)->post(route('admin.winners.store'), winnerFormData($this->category))->assertSessionHasNoErrors();

    expect(Winner::orderBy('id')->pluck('verification_slug')->all())->toBe([
        'airport-online-best-digital-airport-services-marketplace-2026',
        'airport-online-best-digital-airport-services-marketplace-2026-2',
    ]);
});

test('store redirects to edit page with badge and verification box ready', function () {
    $response = $this->actingAs(User::factory()->create())
        ->post(route('admin.winners.store'), winnerFormData($this->category));

    $winner = Winner::first();

    $response->assertRedirect(route('admin.winners.edit', $winner));

    $this->get(route('admin.winners.edit', $winner))
        ->assertSee('Kazanan eklendi.')
        ->assertSee('Rozet ve Doğrulama')
        ->assertSee(route('site.verify', $winner->verification_slug))
        ->assertSee('id="embed_code"', false);
});

test('update keeps the existing slug when company name and year change', function () {
    $winner = Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'verification_slug' => 'original-slug',
    ]);

    $this->actingAs(User::factory()->create())
        ->put(route('admin.winners.update', $winner), winnerFormData($this->category, [
            'company_name' => 'Renamed Company',
            'won_at' => '2027-01-10',
            'verification_slug' => 'original-slug',
        ]))
        ->assertSessionHasNoErrors();

    expect($winner->fresh()->verification_slug)->toBe('original-slug');
});

test('update rejects a slug used by another winner', function () {
    Winner::factory()->create(['verification_slug' => 'taken-slug']);
    $winner = Winner::factory()->create(['category_page_id' => $this->category->id]);

    $this->actingAs(User::factory()->create())
        ->put(route('admin.winners.update', $winner), winnerFormData($this->category, ['verification_slug' => 'taken-slug']))
        ->assertSessionHasErrors('verification_slug');
});

test('admin edit page shows embed code with relative badge src', function () {
    $winner = Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'verification_slug' => 'airport-online-2026',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.winners.edit', $winner))
        ->assertOk()
        ->assertSee(route('site.verify', 'airport-online-2026'))
        ->assertSee('src=&quot;/uploads/airport-online-winner-2026.png&quot;', false)
        ->assertDontSee('src=&quot;http', false)
        ->assertSee('rel=&quot;noopener&quot;', false)
        ->assertDontSee('rel=&quot;noreferrer&quot;', false);
});

test('verify page renders for an active winner', function () {
    $winner = Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'company_name' => 'Airport.online',
        'verification_slug' => 'airport-online-2026',
    ]);

    $this->get('/verify/airport-online-2026')
        ->assertOk()
        ->assertViewIs('site.verify')
        ->assertSee('Airport.online')
        ->assertSee('Best Digital Airport Services Marketplace')
        ->assertSee('2026')
        ->assertSee('/uploads/airport-online-winner-2026.png')
        ->assertSee('<meta name="robots" content="index,follow">', false)
        ->assertSee('<link rel="canonical" href="'.route('site.verify', $winner->verification_slug).'">', false)
        ->assertSee('Award record verified');
});

test('verify page returns 404 for inactive winner', function () {
    Winner::factory()->inactive()->create(['verification_slug' => 'hidden-winner']);

    $this->get('/verify/hidden-winner')->assertNotFound();
});

test('verify page returns 404 for unknown slug', function () {
    $this->get('/verify/does-not-exist')->assertNotFound();
});

test('verify page shows no referrer messages from any source', function () {
    Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'verification_slug' => 'airport-online-2026',
    ]);

    foreach ([null, 'https://airport.online/', 'https://other-site.com/'] as $referer) {
        $request = $referer ? $this->withHeader('referer', $referer) : $this;

        $request->get('/verify/airport-online-2026')
            ->assertOk()
            ->assertSee('Award record verified')
            ->assertDontSee('source website')
            ->assertDontSee('referring website')
            ->assertDontSee('Registered website');
    }
});

test('verify response cannot be framed', function () {
    Winner::factory()->create(['verification_slug' => 'airport-online-2026']);

    $this->get('/verify/airport-online-2026')
        ->assertOk()
        ->assertHeader('X-Frame-Options', 'DENY');
});

test('verify page does not show a website link or certificate', function () {
    Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'verification_slug' => 'airport-online-2026',
    ]);

    $this->get('/verify/airport-online-2026')
        ->assertOk()
        ->assertDontSee('Visit website')
        ->assertDontSee('Certificate');
});

test('admin edit page does not show removed verification messaging', function () {
    $winner = Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'verification_slug' => 'airport-online-2026',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.winners.edit', $winner))
        ->assertOk()
        ->assertDontSee('Test Adımları')
        ->assertDontSee('Kurulum Talimatı')
        ->assertDontSee('Verified source website')
        ->assertDontSee('Sertifika');
});

test('admin index shows verification link', function () {
    Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'verification_slug' => 'airport-online-2026',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.winners.index'))
        ->assertOk()
        ->assertSee(route('site.verify', 'airport-online-2026'));
});

test('verify route is not handled by the catch-all page route', function () {
    expect(app('router')->getRoutes()->match(request()->create('/verify/some-slug'))->getName())
        ->toBe('site.verify');
});

test('verify page works without a badge image', function () {
    $category = Page::factory()->create(['badge_image' => null]);

    Winner::factory()->create([
        'category_page_id' => $category->id,
        'verification_slug' => 'no-badge-winner',
    ]);

    $this->get('/verify/no-badge-winner')
        ->assertOk()
        ->assertSee('verify__badge-empty', false);
});

test('winners page shows no website or verification link', function () {
    Page::factory()->create(['title' => 'Winners', 'slug' => 'winners']);

    Winner::factory()->create([
        'category_page_id' => $this->category->id,
        'verification_slug' => 'airport-online-2026',
    ]);

    $this->get('/winners')
        ->assertOk()
        ->assertDontSee('View verification')
        ->assertDontSee('/verify/')
        ->assertDontSee('Visit website');
});
