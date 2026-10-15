<?php

namespace App\Http\Controllers;

use App\Mail\EntrySubmitted;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class EntrySubmissionController extends Controller
{
    private const THANKS_SLUG = 'enter/thanks';

    public function store(Request $request): RedirectResponse
    {
        if (filled($request->input('company_site_2'))) {
            return redirect()->route('site.page', self::THANKS_SLUG);
        }

        $awardCategories = $this->activeAwardCategories();

        $validated = $request->validate([
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['string', 'distinct', Rule::in(array_keys($awardCategories))],
            'company' => ['required', 'string', 'max:255'],
            'website' => ['required', 'url:http,https', 'max:2048'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'company_type' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2500'],
            'evidence' => ['nullable', 'array', 'max:'.config('awards.entry_max_files')],
            'evidence.*' => ['file', 'max:'.config('awards.entry_max_file_kb'), 'mimes:pdf,png,jpg,jpeg,docx,pptx,xlsx'],
            'consent' => ['accepted'],
        ], [
            'categories.required' => 'Choose at least one category.',
            'consent.accepted' => 'Please confirm the information is accurate and that you are authorised to enter.',
        ], [
            'evidence.*' => 'supporting file',
            'company' => 'company name',
            'contact_name' => 'contact name',
            'company_type' => 'type of company',
            'description' => 'application text',
        ]);

        $files = array_values($request->file('evidence', []));
        $this->ensureTotalSizeFits($files);

        $recipient = config('awards.entry_recipient');

        if (blank($recipient)) {
            Log::error('Entry form submitted but ENTRY_NOTIFY_EMAIL is not configured.');

            return $this->failed();
        }

        $categories = [];

        foreach ($validated['categories'] as $slug) {
            $categories[] = $awardCategories[$slug];
        }

        try {
            Mail::to($recipient)->send(new EntrySubmitted(
                entry: Arr::only($validated, ['company', 'website', 'contact_name', 'email', 'country', 'company_type', 'description']),
                categories: $categories,
                files: $files,
            ));
        } catch (Throwable $exception) {
            report($exception);

            return $this->failed();
        }

        return redirect()->route('site.page', self::THANKS_SLUG);
    }

    private function activeAwardCategories(): array
    {
        $categoriesRoot = Page::categoriesRoot();

        if (! $categoriesRoot) {
            return [];
        }

        $categories = [];

        foreach ($categoriesRoot->activeChildren()->get() as $family) {
            foreach ($family->activeChildren()->get() as $award) {
                $categories[basename($award->slug)] = ['title' => $award->title, 'family' => $family->title];
            }
        }

        return $categories;
    }

    private function ensureTotalSizeFits(array $files): void
    {
        $totalBytes = 0;

        foreach ($files as $file) {
            $totalBytes += $file->getSize();
        }

        $totalKb = $totalBytes / 1024;
        $limitKb = config('awards.entry_max_total_kb');

        if ($totalKb > $limitKb) {
            throw ValidationException::withMessages([
                'evidence' => 'Your files add up to '.round($totalKb / 1024, 1).' MB. The combined limit is '.round($limitKb / 1024).' MB, so please remove or compress some files.',
            ]);
        }
    }

    private function failed(): RedirectResponse
    {
        return back()->withInput()->withErrors([
            'submit' => 'We could not send your application just now. Your answers have been kept: please re-attach your files and try again in a few minutes.',
        ]);
    }
}
