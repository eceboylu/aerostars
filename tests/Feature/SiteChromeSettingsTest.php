<?php

use App\Models\HomeSection;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->admin = User::factory()->create();
});

test('the header button and footer texts come from the database after migrating', function () {
    expect(SiteSetting::getMany(array_merge(SiteSetting::HEADER_KEYS, SiteSetting::FOOTER_KEYS)))->toBe([
        'site_logo' => '/uploads/flyoneers-logo-2027.png',
        'header_cta_label' => 'Enter the awards',
        'header_cta_url' => '/enter/',
        'footer_description' => 'International awards for the companies that sell, provide and manage airport passenger services.',
        'footer_copyright' => '© 2027 Flyoneers. All rights reserved.',
        'footer_email' => 'awards@flyoneers.example',
    ]);

    $this->get('/')->assertOk()
        ->assertSee('<a class="btn btn--signal" href="/enter/">Enter the awards</a>', false)
        ->assertSee('href="mailto:awards@flyoneers.example"', false)
        ->assertDontSee('awards@@', false);
});

test('the header button text and link are managed from the header menu screen', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.header-menu.index'))
        ->assertOk()
        ->assertSee('name="header_cta_label"', false)
        ->assertSee('value="Enter the awards"', false);

    $this->actingAs($this->admin)
        ->put(route('admin.header-menu.settings'), ['header_cta_label' => 'Apply now', 'header_cta_url' => 'https://apply.example.com/'])
        ->assertRedirect(route('admin.header-menu.index'))
        ->assertSessionHasNoErrors();

    $this->get('/')->assertOk()
        ->assertSee('<a class="btn btn--signal" href="https://apply.example.com/">Apply now</a>', false)
        ->assertDontSee('Enter the awards');

    $this->actingAs($this->admin)
        ->put(route('admin.header-menu.settings'), ['header_cta_label' => '', 'header_cta_url' => ''])
        ->assertSessionHasNoErrors();

    $this->get('/')->assertOk()->assertDontSee('Apply now')->assertDontSee('btn btn--signal" href="https://apply.example.com/"', false);
});

test('unsafe or incomplete header button values are rejected', function (array $input, string $errorField) {
    $this->actingAs($this->admin)
        ->put(route('admin.header-menu.settings'), $input)
        ->assertSessionHasErrors($errorField);

    expect(SiteSetting::get('header_cta_url'))->toBe('/enter/');
})->with([
    'javascript link' => [['header_cta_label' => 'Go', 'header_cta_url' => 'javascript:alert(1)'], 'header_cta_url'],
    'label without link' => [['header_cta_label' => 'Go', 'header_cta_url' => ''], 'header_cta_url'],
    'link without label' => [['header_cta_label' => '', 'header_cta_url' => '/enter/'], 'header_cta_label'],
]);

test('footer description, copyright and email are managed from the footer menu screen', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.footer-menu.index'))
        ->assertOk()
        ->assertSee('name="footer_description"', false)
        ->assertSee('name="footer_copyright"', false)
        ->assertSee('name="footer_email"', false);

    $this->actingAs($this->admin)
        ->put(route('admin.footer-menu.settings'), [
            'footer_description' => 'Celebrating the best of airport travel.',
            'footer_copyright' => '© 2028 Flyoneers Ltd.',
            'footer_email' => 'hello@flyoneers.example',
        ])
        ->assertRedirect(route('admin.footer-menu.index'))
        ->assertSessionHasNoErrors();

    $this->get('/')->assertOk()
        ->assertSee('Celebrating the best of airport travel.')
        ->assertSee('© 2028 Flyoneers Ltd.')
        ->assertSee('href="mailto:hello@flyoneers.example"', false)
        ->assertDontSee('awards@flyoneers.example');

    $this->actingAs($this->admin)
        ->put(route('admin.footer-menu.settings'), ['footer_description' => '', 'footer_copyright' => '', 'footer_email' => ''])
        ->assertSessionHasNoErrors();

    $this->get('/')->assertOk()
        ->assertDontSee('Celebrating the best of airport travel.')
        ->assertDontSee('mailto:', false)
        ->assertDontSee('class="legal"', false);
});

test('an invalid footer email is rejected', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.footer-menu.settings'), ['footer_email' => 'awards@@flyoneers'])
        ->assertSessionHasErrors('footer_email');

    expect(SiteSetting::get('footer_email'))->toBe('awards@flyoneers.example');
});

test('guests cannot change header or footer settings', function () {
    $this->put(route('admin.header-menu.settings'), ['header_cta_label' => 'X', 'header_cta_url' => '/x'])->assertRedirect(route('admin.login'));
    $this->put(route('admin.footer-menu.settings'), ['footer_email' => 'x@example.com'])->assertRedirect(route('admin.login'));

    expect(SiteSetting::get('header_cta_label'))->toBe('Enter the awards')
        ->and(SiteSetting::get('footer_email'))->toBe('awards@flyoneers.example');
});

test('the hero category count follows the active categories in the database', function () {
    HomeSection::create(['key' => 'hero', 'label' => 'Hero', 'mode' => 'default', 'published_mode' => 'default', 'status' => true, 'sort_order' => 0]);

    $root = Page::factory()->create(['slug' => 'categories']);
    $family = Page::factory()->create(['parent_id' => $root->id]);
    $inactiveFamily = Page::factory()->create(['parent_id' => $root->id, 'status' => false]);
    $awards = Page::factory()->count(3)->create(['parent_id' => $family->id]);
    Page::factory()->create(['parent_id' => $inactiveFamily->id]);

    $this->get('/')->assertOk()->assertSee('Browse 3 categories');

    Page::factory()->create(['parent_id' => $family->id]);
    $this->get('/')->assertOk()->assertSee('Browse 4 categories');

    $awards->first()->update(['status' => false]);
    $this->get('/')->assertOk()->assertSee('Browse 3 categories');

    Page::where('parent_id', $family->id)->where('status', true)->limit(2)->get()->each->update(['status' => false]);
    $this->get('/')->assertOk()->assertSee('Browse 1 category');
});

test('the logo and hero image come from the database after migrating', function () {
    HomeSection::create(['key' => 'hero', 'label' => 'Hero', 'mode' => 'default', 'published_mode' => 'default', 'status' => true, 'sort_order' => 1]);

    expect(SiteSetting::get('hero_emblem'))->toBe('/uploads/flyoneers-emblem.png');

    $this->get('/')->assertOk()
        ->assertSee('<img src="/uploads/flyoneers-logo-2027.png" alt="Flyoneers"', false)
        ->assertSee('class="hero__emblem" src="/uploads/flyoneers-emblem.png"', false);

    $this->get(route('admin.login'))->assertOk()
        ->assertSee('src="/uploads/flyoneers-logo-2027.png"', false);
});

test('the logo is changed and removed from the header menu screen', function () {
    Storage::fake('uploads');
    Storage::disk('uploads')->put('new-logo.png', 'png');

    $this->actingAs($this->admin)
        ->put(route('admin.header-menu.settings'), ['site_logo' => '/uploads/new-logo.png', 'header_cta_label' => 'Enter the awards', 'header_cta_url' => '/enter/'])
        ->assertSessionHasNoErrors();

    $this->get('/')->assertSee('<img src="/uploads/new-logo.png" alt="Flyoneers"', false);

    $this->actingAs($this->admin)
        ->put(route('admin.header-menu.settings'), ['site_logo' => '', 'header_cta_label' => 'Enter the awards', 'header_cta_url' => '/enter/'])
        ->assertSessionHasNoErrors();

    $this->get('/')->assertSee('<a class="brand is-broken"', false)
        ->assertDontSee('/uploads/new-logo.png', false);
});

test('the logo must be an uploaded image', function (string $logo) {
    Storage::fake('uploads');

    $this->actingAs($this->admin)
        ->put(route('admin.header-menu.settings'), ['site_logo' => $logo, 'header_cta_label' => 'Enter the awards', 'header_cta_url' => '/enter/'])
        ->assertSessionHasErrors('site_logo');

    expect(SiteSetting::get('site_logo'))->toBe('/uploads/flyoneers-logo-2027.png');
})->with(['svg file' => '/uploads/logo.svg', 'outside uploads' => 'https://evil.example/logo.png', 'missing file' => '/uploads/missing.png']);

test('the hero image is changed from the hero section form', function () {
    Storage::fake('uploads');
    Storage::disk('uploads')->put('new-emblem.webp', 'webp');
    $hero = HomeSection::create(['key' => 'hero', 'label' => 'Hero', 'mode' => 'default', 'published_mode' => 'default', 'status' => true, 'sort_order' => 1]);

    $this->actingAs($this->admin)
        ->get(route('admin.home-sections.edit', $hero))
        ->assertOk()
        ->assertSee('name="hero_emblem"', false);

    $heroFields = array_merge(SiteSetting::HOMEPAGE_DEFAULTS, ['label' => 'Hero', 'status' => 1, 'mode' => 'default', 'hero_emblem' => '/uploads/new-emblem.webp']);

    $this->actingAs($this->admin)
        ->put(route('admin.home-sections.update', $hero), $heroFields)
        ->assertSessionHasNoErrors();

    $this->get('/')->assertSee('class="hero__emblem" src="/uploads/new-emblem.webp"', false);
});
