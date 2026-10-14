<?php

use App\Models\HomeSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('html field hidden behind the editor is not marked required in the browser', function () {
    $html = $this->actingAs(User::factory()->create())
        ->get(route('admin.home-sections.create'))
        ->assertOk()
        ->getContent();

    preg_match('/<textarea[^>]*id="page-html-editor"[^>]*>/s', $html, $matches);

    expect($matches)->not->toBeEmpty()
        ->and($matches[0])->not->toContain('required');
});

test('a custom section is stored and published', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.home-sections.store'), [
            'label' => 'Sponsors',
            'custom_html' => '<section class="section"><div class="wrap"><h2>Sponsors</h2></div></section>',
            'custom_css' => '.sponsors { color: red; }',
            'status' => 1,
        ])
        ->assertRedirect(route('admin.home-sections.index'))
        ->assertSessionHasNoErrors();

    $section = HomeSection::where('label', 'Sponsors')->sole();

    expect($section->mode)->toBe('custom')
        ->and($section->published_custom_html)->toContain('<h2>Sponsors</h2>')
        ->and($section->published_custom_css)->toBe('.sponsors { color: red; }')
        ->and($section->status)->toBeTrue();
});

test('html content is required on the server', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.home-sections.store'), [
            'label' => 'Empty',
            'custom_html' => '',
            'status' => 1,
        ])
        ->assertSessionHasErrors('custom_html');

    expect(HomeSection::where('label', 'Empty')->exists())->toBeFalse();
});
