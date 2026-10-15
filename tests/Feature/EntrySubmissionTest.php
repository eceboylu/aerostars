<?php

use App\Mail\EntrySubmitted;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    config(['awards.entry_recipient' => 'entries@flyoneers.test']);

    $root = Page::factory()->create(['title' => 'Categories', 'slug' => 'categories']);
    $family = Page::factory()->create(['title' => 'Travel Technology & Platforms', 'slug' => 'categories/travel-technology-platforms', 'parent_id' => $root->id]);
    Page::factory()->create(['title' => 'Best Airport Travel App', 'slug' => 'categories/travel-technology-platforms/best-airport-travel-app', 'parent_id' => $family->id]);
    Page::factory()->create(['title' => 'Best Airport Travel Planning Tool', 'slug' => 'categories/travel-technology-platforms/best-airport-travel-planning-tool', 'parent_id' => $family->id]);
    Page::factory()->create(['title' => 'Retired Award', 'slug' => 'categories/travel-technology-platforms/retired-award', 'parent_id' => $family->id, 'status' => false]);
    Page::factory()->create(['title' => 'Thanks', 'slug' => 'enter/thanks']);
});

function entryData(array $overrides = []): array
{
    return array_merge([
        'categories' => ['best-airport-travel-app', 'best-airport-travel-planning-tool'],
        'company' => 'Skyline Apps',
        'website' => 'https://skyline.example',
        'contact_name' => 'Ayse Kaya',
        'email' => 'ayse@skyline.example',
        'country' => 'Turkey',
        'company_type' => 'Airport travel app developers',
        'description' => 'We help travellers move through airports faster.',
        'consent' => '1',
    ], $overrides);
}

test('a valid entry is emailed with every answer, the chosen categories and the files attached', function () {
    Mail::fake();

    $this->post(route('site.entry.submit'), entryData([
        'evidence' => [
            UploadedFile::fake()->create('Pitch Deck.pdf', 900, 'application/pdf'),
            UploadedFile::fake()->create('booking-flow.png', 120, 'image/png'),
        ],
    ]))->assertRedirect(route('site.page', 'enter/thanks'));

    Mail::assertSent(EntrySubmitted::class, function (EntrySubmitted $mail) {
        return $mail->hasTo('entries@flyoneers.test')
            && $mail->hasReplyTo('ayse@skyline.example', 'Ayse Kaya')
            && $mail->hasSubject('New Flyoneers entry: Skyline Apps (2 categories)')
            && count($mail->attachments()) === 2
            && $mail->categories === [
                ['title' => 'Best Airport Travel App', 'family' => 'Travel Technology & Platforms'],
                ['title' => 'Best Airport Travel Planning Tool', 'family' => 'Travel Technology & Platforms'],
            ]
            && $mail->entry['company_type'] === 'Airport travel app developers';
    });
});

test('the email lists every field and file name', function () {
    $mail = new EntrySubmitted(
        entry: entryData(),
        categories: [['title' => 'Best Airport Travel App', 'family' => 'Travel Technology & Platforms']],
        files: [UploadedFile::fake()->create('Pitch Deck.pdf', 10, 'application/pdf')],
    );

    $mail->assertSeeInHtml('Skyline Apps')
        ->assertSeeInHtml('https://skyline.example')
        ->assertSeeInHtml('Ayse Kaya')
        ->assertSeeInHtml('ayse@skyline.example')
        ->assertSeeInHtml('Turkey')
        ->assertSeeInHtml('Airport travel app developers')
        ->assertSeeInHtml('We help travellers move through airports faster.')
        ->assertSeeInHtml('Best Airport Travel App')
        ->assertSeeInHtml('pitch-deck.pdf');
});

test('applicant text cannot inject links or html into the email', function () {
    $mail = new EntrySubmitted(
        entry: entryData([
            'company' => '[Click me](https://evil.example)',
            'description' => '<script>alert(1)</script> [phish](https://evil.example)',
        ]),
        categories: [['title' => 'Best Airport Travel App', 'family' => 'Travel Technology & Platforms']],
    );

    $mail->assertDontSeeInHtml('<a href="https://evil.example"', false)
        ->assertDontSeeInHtml('<script>', false);
});

test('a submission without a category is rejected and nothing is sent', function () {
    Mail::fake();

    $this->from('/enter')->post(route('site.entry.submit'), entryData(['categories' => []]))
        ->assertRedirect('/enter')
        ->assertSessionHasErrors('categories');

    Mail::assertNothingSent();
});

test('only active award categories can be chosen', function (string $slug) {
    Mail::fake();

    $this->post(route('site.entry.submit'), entryData(['categories' => [$slug]]))
        ->assertSessionHasErrors('categories.0');

    Mail::assertNothingSent();
})->with(['inactive award' => 'retired-award', 'unknown value' => 'not-a-category']);

test('files of an unsupported type are rejected', function () {
    Mail::fake();

    $this->post(route('site.entry.submit'), entryData(['evidence' => [UploadedFile::fake()->create('virus.exe', 10)]]))
        ->assertSessionHasErrors('evidence.0');

    Mail::assertNothingSent();
});

test('more than five files are rejected', function () {
    Mail::fake();

    $files = array_map(fn ($i) => UploadedFile::fake()->create("file{$i}.pdf", 10, 'application/pdf'), range(1, 6));

    $this->post(route('site.entry.submit'), entryData(['evidence' => $files]))->assertSessionHasErrors('evidence');

    Mail::assertNothingSent();
});

test('files over the combined size limit are rejected', function () {
    Mail::fake();

    $files = [
        UploadedFile::fake()->create('a.pdf', 9000, 'application/pdf'),
        UploadedFile::fake()->create('b.pdf', 9000, 'application/pdf'),
        UploadedFile::fake()->create('c.pdf', 1000, 'application/pdf'),
    ];

    $this->post(route('site.entry.submit'), entryData(['evidence' => $files]))->assertSessionHasErrors('evidence');

    Mail::assertNothingSent();
});

test('the consent box must be ticked', function () {
    Mail::fake();

    $this->post(route('site.entry.submit'), entryData(['consent' => null]))->assertSessionHasErrors('consent');

    Mail::assertNothingSent();
});

test('a bot filling the hidden field is sent to the thank-you page without an email', function () {
    Mail::fake();

    $this->post(route('site.entry.submit'), entryData(['company_site_2' => 'spam']))
        ->assertRedirect(route('site.page', 'enter/thanks'));

    Mail::assertNothingSent();
});

test('the applicant is told when no recipient is configured', function () {
    Mail::fake();
    config(['awards.entry_recipient' => null]);

    $this->from('/enter')->post(route('site.entry.submit'), entryData())
        ->assertRedirect('/enter')
        ->assertSessionHasErrors('submit');

    Mail::assertNothingSent();
});

test('the applicant is told when the email cannot be sent and their answers are kept', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down'));

    $this->from('/enter')->post(route('site.entry.submit'), entryData())
        ->assertRedirect('/enter')
        ->assertSessionHasErrors('submit')
        ->assertSessionHasInput('company', 'Skyline Apps');
});

test('the entry page shows errors and hands the answers back to the form', function () {
    Mail::fake();
    Page::factory()->create(['title' => 'Enter', 'slug' => 'enter', 'custom_html' => '<form id="entry-form" method="post" action="/enter/submit"></form>']);

    $this->from('/enter')
        ->followingRedirects()
        ->post(route('site.entry.submit'), entryData(['categories' => []]))
        ->assertOk()
        ->assertSee('Choose at least one category.')
        ->assertSee('window.flyOldInput', false)
        ->assertSee('"company":"Skyline Apps"', false)
        ->assertSee('<meta name="csrf-token"', false);

    Mail::assertNothingSent();
});
