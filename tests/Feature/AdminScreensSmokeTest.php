<?php

use App\Models\HomeSection;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('uploads');
});

test('admin screens still load for a signed-in admin', function (string $route, bool $needsPage) {
    $page = Page::factory()->create();
    $section = HomeSection::create(['key' => 'faq', 'label' => 'FAQ', 'mode' => 'default', 'published_mode' => 'default', 'status' => true, 'sort_order' => 1]);

    $parameters = match ($route) {
        'admin.pages.edit' => [$page->id],
        'admin.home-sections.edit' => [$section->id],
        default => [],
    };

    $this->actingAs(User::factory()->create())
        ->get(route($route, $parameters))
        ->assertOk();
})->with([
    'dashboard' => ['admin.pages.dashboard', false],
    'pages index' => ['admin.pages.index', false],
    'page create' => ['admin.pages.create', false],
    'page edit' => ['admin.pages.edit', true],
    'header menu' => ['admin.header-menu.index', false],
    'footer menu' => ['admin.footer-menu.index', false],
    'home sections' => ['admin.home-sections.index', false],
    'home section edit' => ['admin.home-sections.edit', false],
    'homepage override' => ['admin.homepage-override.edit', false],
    'site assets' => ['admin.site-assets.edit', false],
    'file manager' => ['admin.filemanager.index', false],
    'winners' => ['admin.winners.index', false],
    'winner create' => ['admin.winners.create', false],
]);

test('admin screens require a login', function () {
    $this->get(route('admin.pages.index'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.home-sections.index'))->assertRedirect(route('admin.login'));
});

test('admin login page still renders', function () {
    $this->get(route('admin.login'))->assertOk()->assertSee('Admin Giriş', false);
});
