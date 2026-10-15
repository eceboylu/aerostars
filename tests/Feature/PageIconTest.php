<?php

use App\Models\HomeSection;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();

    Artisan::shouldReceive('call')->with('pages:compile-cms-assets')->andReturn(0);

    Storage::fake('uploads');
    Storage::disk('uploads')->put('ground-icon.png', 'png');
    Storage::disk('uploads')->put('ground-icon-2.webp', 'webp');

    $this->categoriesRoot = Page::factory()->create(['title' => 'Categories', 'slug' => 'categories']);
    $this->family = Page::factory()->create([
        'title' => 'Airport Ground Services',
        'slug' => 'categories/airport-ground-services',
        'parent_id' => $this->categoriesRoot->id,
        'meta_description' => 'Recognising the services that help travellers reach the airport.',
    ]);
    $this->award = Page::factory()->create([
        'title' => 'Best Airport Transfer Service',
        'slug' => 'categories/airport-ground-services/best-airport-transfer-service',
        'parent_id' => $this->family->id,
    ]);

    HomeSection::create([
        'key' => 'families',
        'label' => 'Award Families',
        'mode' => 'default',
        'published_mode' => 'default',
        'status' => true,
        'sort_order' => 1,
    ]);
});

function pageFormData(Page $page, array $overrides = []): array
{
    return array_merge([
        'parent_id' => $page->parent_id,
        'title' => $page->title,
        'slug' => $page->slug,
        'status' => 1,
        'robots_index' => 1,
    ], $overrides);
}

test('guests cannot change a page icon', function () {
    $this->put(route('admin.pages.update', $this->family->id), pageFormData($this->family, ['icon' => '/uploads/ground-icon.png']))
        ->assertRedirect(route('admin.login'));

    expect($this->family->fresh()->icon)->toBeNull();
});

test('the page edit form shows the icon field with picker and remove buttons', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.pages.edit', $this->family->id))
        ->assertOk()
        ->assertSee('name="icon"', false)
        ->assertSee('data-input="icon"', false)
        ->assertSee('js-clear-image', false);
});

test('admin can upload, change and remove a family icon and the homepage follows', function () {
    $admin = User::factory()->create();

    $this->get('/')->assertOk()->assertSee('class="family-icon"', false)->assertDontSee('/uploads/ground-icon.png');

    $this->actingAs($admin)
        ->put(route('admin.pages.update', $this->family->id), pageFormData($this->family, ['icon' => '/uploads/ground-icon.png']))
        ->assertSessionHasNoErrors()
        ->assertRedirect();
    expect($this->family->fresh()->icon)->toBe('/uploads/ground-icon.png');
    $this->get('/')->assertOk()->assertSee('src="/uploads/ground-icon.png"', false);

    $this->actingAs($admin)
        ->put(route('admin.pages.update', $this->family->id), pageFormData($this->family, ['icon' => '/uploads/ground-icon-2.webp']))
        ->assertSessionHasNoErrors();
    $this->get('/')->assertOk()
        ->assertSee('src="/uploads/ground-icon-2.webp"', false)
        ->assertDontSee('/uploads/ground-icon.png');

    $this->actingAs($admin)
        ->put(route('admin.pages.update', $this->family->id), pageFormData($this->family, ['icon' => '']))
        ->assertSessionHasNoErrors();
    expect($this->family->fresh()->icon)->toBeNull();
    $this->get('/')->assertOk()
        ->assertDontSee('/uploads/ground-icon-2.webp')
        ->assertSee('<svg class="family-icon"', false);
});

test('a family icon is shown on the categories and family pages', function () {
    $this->family->update(['icon' => '/uploads/ground-icon.png']);

    $this->get('/categories')->assertOk()
        ->assertSee('src="/uploads/ground-icon.png"', false)
        ->assertSee('Best Airport Transfer Service');

    $this->get('/categories/airport-ground-services')->assertOk()
        ->assertSee('src="/uploads/ground-icon.png"', false)
        ->assertSee('Airport Ground Services awards');
});

test('an award category icon is only shown when one is set', function () {
    $this->get('/categories/airport-ground-services')->assertOk()->assertDontSee('class="row__icon"', false);

    $this->award->update(['icon' => '/uploads/ground-icon-2.webp']);

    $this->get('/categories/airport-ground-services')->assertOk()
        ->assertSee('class="row__icon"', false)
        ->assertSee('src="/uploads/ground-icon-2.webp"', false);
});

test('an icon can be set when creating a page', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.pages.store'), [
            'parent_id' => $this->categoriesRoot->id,
            'title' => 'Travel Guides & Content',
            'slug' => 'categories/travel-guides-content',
            'status' => 1,
            'robots_index' => 1,
            'icon' => '/uploads/ground-icon.png',
        ])
        ->assertSessionHasNoErrors();

    expect(Page::where('slug', 'categories/travel-guides-content')->sole()->icon)->toBe('/uploads/ground-icon.png');
});

test('unsafe or invalid icon values are rejected', function (string $icon) {
    $this->actingAs(User::factory()->create())
        ->put(route('admin.pages.update', $this->family->id), pageFormData($this->family, ['icon' => $icon]))
        ->assertSessionHasErrors('icon');

    expect($this->family->fresh()->icon)->toBeNull();
})->with([
    'external url' => 'https://evil.example/icon.png',
    'svg' => '/uploads/ground-icon.svg',
    'path traversal' => '/uploads/../.env',
    'not an image' => '/uploads/notes.php',
    'outside uploads' => '/build/icon.png',
    'missing file' => '/uploads/does-not-exist.png',
    'javascript url' => 'javascript:alert(1)',
]);
