<?php

use App\Models\HomeSection;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();

    Artisan::shouldReceive('call')->with('pages:compile-cms-assets')->andReturn(0);

    $this->admin = User::factory()->create();
});

test('a page added, edited and deleted in the admin follows on the public site', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.pages.store'), [
            'title' => 'Press Room',
            'slug' => 'press-room',
            'status' => 1,
            'robots_index' => 1,
            'custom_html' => '<section class="section"><div class="wrap"><p>Original press text</p></div></section>',
        ])
        ->assertSessionHasNoErrors();

    $page = Page::where('slug', 'press-room')->sole();
    $this->get('/press-room')->assertOk()->assertSee('Original press text');

    $this->actingAs($this->admin)
        ->put(route('admin.pages.update', $page->id), [
            'title' => 'Press Room',
            'slug' => 'press-room',
            'status' => 1,
            'robots_index' => 1,
            'custom_html' => '<section class="section"><div class="wrap"><p>Updated press text</p></div></section>',
        ])
        ->assertSessionHasNoErrors();

    $this->get('/press-room')->assertOk()->assertSee('Updated press text')->assertDontSee('Original press text');

    $this->actingAs($this->admin)->delete(route('admin.pages.destroy', $page->id));

    expect(Page::where('slug', 'press-room')->exists())->toBeFalse();
    $this->get('/press-room')->assertNotFound();
});

test('a header menu item added, renamed and deleted in the admin follows in the site header', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.header-menu.store'), [
            'link_type' => 'custom',
            'custom_url' => 'https://example.com/press',
            'label' => 'Press',
            'status' => 1,
        ])
        ->assertSessionHasNoErrors();

    $item = MenuItem::where('label', 'Press')->sole();
    $this->get('/')->assertOk()->assertSee('>Press</a>', false);

    $this->actingAs($this->admin)
        ->put(route('admin.header-menu.update', $item->id), [
            'link_type' => 'custom',
            'custom_url' => 'https://example.com/press',
            'label' => 'Newsroom',
            'status' => 1,
        ])
        ->assertSessionHasNoErrors();

    $this->get('/')->assertOk()->assertSee('>Newsroom</a>', false)->assertDontSee('>Press</a>', false);

    $this->actingAs($this->admin)->delete(route('admin.header-menu.destroy', $item->id));

    expect(MenuItem::find($item->id))->toBeNull();
    $this->get('/')->assertOk()->assertDontSee('>Newsroom</a>', false);
});

test('a winner added, edited and deleted in the admin follows on the winners page', function () {
    Page::factory()->create(['title' => 'Winners', 'slug' => 'winners']);
    $category = Page::factory()->create(['title' => 'Best Airport Travel App', 'slug' => 'best-airport-travel-app']);

    $this->actingAs($this->admin)
        ->post(route('admin.winners.store'), [
            'category_page_id' => $category->id,
            'company_name' => 'Skyline Apps',
            'won_at' => '2027-10-20',
            'status' => 1,
        ])
        ->assertSessionHasNoErrors();

    $winner = Winner::where('company_name', 'Skyline Apps')->sole();
    $this->get('/winners')->assertOk()->assertSee('Skyline Apps');

    $this->actingAs($this->admin)
        ->put(route('admin.winners.update', $winner), [
            'category_page_id' => $category->id,
            'company_name' => 'Skyline Mobility',
            'verification_slug' => $winner->verification_slug,
            'won_at' => '2027-10-20',
            'status' => 1,
        ])
        ->assertSessionHasNoErrors();

    $this->get('/winners')->assertOk()->assertSee('Skyline Mobility')->assertDontSee('Skyline Apps');

    $this->actingAs($this->admin)->delete(route('admin.winners.destroy', $winner));

    expect(Winner::find($winner->id))->toBeNull();
    $this->get('/winners')->assertOk()->assertDontSee('Skyline Mobility');
});

test('hero text saved in the admin is stored in the database and shown on the homepage', function () {
    $hero = HomeSection::create(['key' => 'hero', 'label' => 'Hero', 'mode' => 'default', 'published_mode' => 'default', 'status' => true, 'sort_order' => 0]);

    $this->actingAs($this->admin)
        ->put(route('admin.home-sections.update', $hero), [
            'label' => 'Hero',
            'status' => 1,
            'mode' => 'default',
            'hero_eyebrow' => 'Awards 2028',
            'hero_title' => 'A brand new headline',
            'hero_lede' => 'A brand new lede.',
            'stat_1_num' => '20', 'stat_1_label' => 'Award categories',
            'stat_2_num' => '5', 'stat_2_label' => 'Award families',
            'stat_3_num' => '12', 'stat_3_label' => 'Judging criteria',
            'stat_4_num' => '1', 'stat_4_label' => 'Winner per category',
        ])
        ->assertSessionHasNoErrors();

    expect(SiteSetting::get('hero_title'))->toBe('A brand new headline');

    $this->get('/')->assertOk()
        ->assertSee('A brand new headline')
        ->assertSee('Awards 2028')
        ->assertSee('>20<', false)
        ->assertSee('>05<', false);
});

test('jury members saved as a draft only go live after publishing', function () {
    $jurors = HomeSection::create(['key' => 'jurors', 'label' => 'Jury Members', 'mode' => 'default', 'published_mode' => 'default', 'status' => true, 'sort_order' => 1]);

    $this->actingAs($this->admin)
        ->put(route('admin.home-sections.update', $jurors), [
            'label' => 'Jury Members',
            'status' => 1,
            'mode' => 'default',
            'heading' => 'Meet the jury',
            'paragraph' => 'Independent experts.',
            'member_1_name' => 'Ayse Kaya',
            'member_1_role' => 'Head of Travel Tech',
            'member_1_org' => 'Example Air',
        ])
        ->assertSessionHasNoErrors();

    expect($jurors->fresh()->settings['members'][0]['name'])->toBe('Ayse Kaya');
    $this->get('/')->assertOk()->assertDontSee('Ayse Kaya');

    $this->actingAs($this->admin)->post(route('admin.home-sections.publish', $jurors));

    $this->get('/')->assertOk()->assertSee('Ayse Kaya')->assertSee('Head of Travel Tech');
});
