<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    private const HERO_KEYS = [
        'hero_eyebrow', 'hero_title', 'hero_lede',
        'stat_1_num', 'stat_1_label',
        'stat_2_num', 'stat_2_label',
        'stat_3_num', 'stat_3_label',
        'stat_4_num', 'stat_4_label',
    ];

    private const CTA_KEYS = ['closing_title', 'closing_text', 'closing_button_1_text', 'closing_button_2_text'];

    private const KEY_DATES_KEYS = ['heading', 'paragraph', 'button_1_text', 'button_2_text'];

    private const KEY_DATES_MAX_ROWS = 8;

    private const COMPARISON_KEYS = ['heading', 'paragraph', 'left_heading', 'right_heading'];

    private const COMPARISON_MAX_ITEMS = 6;

    private const TICKER_MAX_ITEMS = 12;

    private const ENTER_LIST_KEYS = ['heading', 'note'];

    private const ENTER_LIST_MAX_ITEMS = 16;

    private const PROCESS_KEYS = ['heading', 'paragraph', 'note_text'];

    private const PROCESS_MAX_STEPS = 12;

    private const CRITERIA_KEYS = ['heading', 'paragraph', 'note_text'];

    private const CRITERIA_MAX_ITEMS = 14;

    private const JURORS_KEYS = ['heading', 'paragraph'];

    private const JURORS_MAX_MEMBERS = 8;

    public function index()
    {
        $sections = HomeSection::orderBy('sort_order')->get();

        return view('admin.home-sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.home-sections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'custom_html' => 'required|string',
            'custom_css' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $validated['key'] = null;
        $validated['mode'] = 'custom';
        $validated['sort_order'] = (int) (HomeSection::max('sort_order') ?? 0) + 1;
        $validated['published_mode'] = $validated['mode'];
        $validated['published_custom_html'] = $validated['custom_html'];
        $validated['published_custom_css'] = $validated['custom_css'];

        HomeSection::create($validated);

        return redirect()
            ->route('admin.home-sections.index')
            ->with('status', 'Yeni bölüm eklendi.');
    }

    public function edit(HomeSection $homeSection)
    {
        $settingsFields = null;

        if ($homeSection->key === 'hero') {
            $settingsFields = SiteSetting::getMany(self::HERO_KEYS);
        } elseif ($homeSection->key === 'cta') {
            $settingsFields = SiteSetting::getMany(self::CTA_KEYS);
        }

        if ($settingsFields) {
            foreach ($settingsFields as $key => $value) {
                $settingsFields[$key] = $value ?? SiteSetting::HOMEPAGE_DEFAULTS[$key];
            }
        }

        if ($homeSection->key === 'hero') {
            $settingsFields['hero_emblem'] = SiteSetting::get('hero_emblem');
        }

        if ($homeSection->key === 'ticker') {
            $data = $homeSection->settingsWithDefaults();
            $settingsFields = [];

            foreach ($data['items'] as $i => $item) {
                $settingsFields['item_'.($i + 1)] = $item;
            }
        }

        if ($homeSection->key === 'enter_list') {
            $data = $homeSection->settingsWithDefaults();
            $settingsFields = array_intersect_key($data, array_flip(self::ENTER_LIST_KEYS));

            foreach ($data['items'] as $i => $item) {
                $settingsFields['item_'.($i + 1)] = $item;
            }
        }

        if ($homeSection->key === 'process') {
            $data = $homeSection->settingsWithDefaults();
            $settingsFields = array_intersect_key($data, array_flip(self::PROCESS_KEYS));

            foreach ($data['steps'] as $i => $step) {
                $n = $i + 1;
                $settingsFields["step_{$n}_title"] = $step['title'];
                $settingsFields["step_{$n}_text"] = $step['text'];
            }
        }

        if ($homeSection->key === 'criteria') {
            $data = $homeSection->settingsWithDefaults();
            $settingsFields = array_intersect_key($data, array_flip(self::CRITERIA_KEYS));

            foreach ($data['items'] as $i => $item) {
                $n = $i + 1;
                $settingsFields["item_{$n}_title"] = $item['title'];
                $settingsFields["item_{$n}_text"] = $item['text'];
            }
        }

        if ($homeSection->key === 'jurors') {
            $data = $homeSection->settingsWithDefaults();
            $settingsFields = array_intersect_key($data, array_flip(self::JURORS_KEYS));

            foreach ($data['members'] as $i => $member) {
                $n = $i + 1;
                $settingsFields["member_{$n}_name"] = $member['name'];
                $settingsFields["member_{$n}_role"] = $member['role'];
                $settingsFields["member_{$n}_org"] = $member['org'];
                $settingsFields["member_{$n}_image"] = $member['image'] ?? null;
            }
        }

        if ($homeSection->key === 'key_dates') {
            $data = $homeSection->settingsWithDefaults();
            $settingsFields = array_intersect_key($data, array_flip(self::KEY_DATES_KEYS));

            foreach ($data['dates'] as $i => $date) {
                $n = $i + 1;
                $settingsFields["date_{$n}_time"] = $date['time'];
                $settingsFields["date_{$n}_label"] = $date['label'];
                $settingsFields["date_{$n}_status"] = $date['status'];
            }
        }

        if ($homeSection->key === 'comparison') {
            $data = $homeSection->settingsWithDefaults();
            $settingsFields = array_intersect_key($data, array_flip(self::COMPARISON_KEYS));

            foreach ($data['left_items'] as $i => $item) {
                $settingsFields['left_item_'.($i + 1)] = $item;
            }

            foreach ($data['right_items'] as $i => $item) {
                $settingsFields['right_item_'.($i + 1)] = $item;
            }
        }

        $htmlSeed = $homeSection->custom_html;

        if (! $htmlSeed && $homeSection->partialView()) {
            $seedData = in_array($homeSection->key, ['hero', 'cta'], true)
                ? $settingsFields
                : $homeSection->settingsWithDefaults();

            $htmlSeed = $seedData
                ? view($homeSection->partialView(), $seedData)->render()
                : $homeSection->htmlSeed();
        }

        return view('admin.home-sections.edit', [
            'section' => $homeSection,
            'settingsFields' => $settingsFields,
            'htmlSeed' => $htmlSeed,
            'cssSeed' => $homeSection->custom_css,
        ]);
    }

    public function update(Request $request, HomeSection $homeSection)
    {
        $rules = [
            'label' => 'required|string|max:255',
            'status' => 'required|boolean',
        ];

        if ($homeSection->isFullyDynamic()) {
            $validated = $request->validate($rules);

            $homeSection->label = $validated['label'];
            $homeSection->status = $validated['status'];
            $homeSection->mode = 'default';
            $homeSection->custom_html = null;
            $homeSection->custom_css = null;
            $homeSection->save();

            return redirect()
                ->route('admin.home-sections.index')
                ->with('status', 'Section updated.');
        }

        if ($homeSection->key) {
            $rules['mode'] = 'required|in:default,custom';
            $rules['custom_html'] = 'required_if:mode,custom|nullable|string';
            $rules['custom_css'] = 'nullable|string';
        } else {
            $rules['custom_html'] = 'required|string';
            $rules['custom_css'] = 'nullable|string';
        }

        if ($homeSection->key === 'hero') {
            $rules += array_fill_keys(self::HERO_KEYS, 'required|string|max:1000');
            $rules['hero_emblem'] = FileManagerController::uploadedImageRules();
        } elseif ($homeSection->key === 'cta') {
            $rules += array_fill_keys(self::CTA_KEYS, 'required|string|max:1000');
        } elseif ($homeSection->key === 'key_dates') {
            $rules += array_fill_keys(self::KEY_DATES_KEYS, 'required|string|max:1000');

            for ($i = 1; $i <= self::KEY_DATES_MAX_ROWS; $i++) {
                $rules["date_{$i}_time"] = 'nullable|string|max:50';
                $rules["date_{$i}_label"] = 'nullable|string|max:255';
                $rules["date_{$i}_status"] = 'nullable|string|max:50';
            }
        } elseif ($homeSection->key === 'comparison') {
            $rules += array_fill_keys(self::COMPARISON_KEYS, 'required|string|max:1000');

            for ($i = 1; $i <= self::COMPARISON_MAX_ITEMS; $i++) {
                $rules["left_item_{$i}"] = 'nullable|string|max:255';
                $rules["right_item_{$i}"] = 'nullable|string|max:255';
            }
        } elseif ($homeSection->key === 'ticker') {
            for ($i = 1; $i <= self::TICKER_MAX_ITEMS; $i++) {
                $rules["item_{$i}"] = 'nullable|string|max:100';
            }
        } elseif ($homeSection->key === 'enter_list') {
            $rules += array_fill_keys(self::ENTER_LIST_KEYS, 'required|string|max:1000');

            for ($i = 1; $i <= self::ENTER_LIST_MAX_ITEMS; $i++) {
                $rules["item_{$i}"] = 'nullable|string|max:255';
            }
        } elseif ($homeSection->key === 'process') {
            $rules += array_fill_keys(self::PROCESS_KEYS, 'required|string|max:1000');

            for ($i = 1; $i <= self::PROCESS_MAX_STEPS; $i++) {
                $rules["step_{$i}_title"] = 'nullable|string|max:255';
                $rules["step_{$i}_text"] = 'nullable|string|max:500';
            }
        } elseif ($homeSection->key === 'criteria') {
            $rules += array_fill_keys(self::CRITERIA_KEYS, 'required|string|max:1000');

            for ($i = 1; $i <= self::CRITERIA_MAX_ITEMS; $i++) {
                $rules["item_{$i}_title"] = 'nullable|string|max:255';
                $rules["item_{$i}_text"] = 'nullable|string|max:500';
            }
        } elseif ($homeSection->key === 'jurors') {
            $rules += array_fill_keys(self::JURORS_KEYS, 'required|string|max:1000');

            for ($i = 1; $i <= self::JURORS_MAX_MEMBERS; $i++) {
                $rules["member_{$i}_name"] = 'nullable|string|max:255';
                $rules["member_{$i}_role"] = 'nullable|string|max:255';
                $rules["member_{$i}_org"] = 'nullable|string|max:255';
                $rules["member_{$i}_image"] = 'nullable|string|max:2048';
            }
        }

        $validated = $request->validate($rules);

        if ($homeSection->key === 'hero') {
            SiteSetting::setMany(array_intersect_key($validated, array_flip(self::HERO_KEYS)));
            SiteSetting::setMany(['hero_emblem' => $validated['hero_emblem'] ?? null]);
        } elseif ($homeSection->key === 'cta') {
            SiteSetting::setMany(array_intersect_key($validated, array_flip(self::CTA_KEYS)));
        } elseif ($homeSection->key === 'key_dates') {
            $homeSection->settings = array_merge(
                array_intersect_key($validated, array_flip(self::KEY_DATES_KEYS)),
                ['dates' => self::buildKeyDatesRows($validated)]
            );
        } elseif ($homeSection->key === 'comparison') {
            $homeSection->settings = array_merge(
                array_intersect_key($validated, array_flip(self::COMPARISON_KEYS)),
                [
                    'left_items' => self::buildListItems($validated, 'left_item_', self::COMPARISON_MAX_ITEMS),
                    'right_items' => self::buildListItems($validated, 'right_item_', self::COMPARISON_MAX_ITEMS),
                ]
            );
        } elseif ($homeSection->key === 'ticker') {
            $homeSection->settings = [
                'items' => self::buildListItems($validated, 'item_', self::TICKER_MAX_ITEMS),
            ];
        } elseif ($homeSection->key === 'enter_list') {
            $homeSection->settings = array_merge(
                array_intersect_key($validated, array_flip(self::ENTER_LIST_KEYS)),
                ['items' => self::buildListItems($validated, 'item_', self::ENTER_LIST_MAX_ITEMS)]
            );
        } elseif ($homeSection->key === 'process') {
            $homeSection->settings = array_merge(
                array_intersect_key($validated, array_flip(self::PROCESS_KEYS)),
                ['steps' => self::buildTitledRows($validated, 'step_', self::PROCESS_MAX_STEPS)]
            );
        } elseif ($homeSection->key === 'criteria') {
            $homeSection->settings = array_merge(
                array_intersect_key($validated, array_flip(self::CRITERIA_KEYS)),
                ['items' => self::buildTitledRows($validated, 'item_', self::CRITERIA_MAX_ITEMS)]
            );
        } elseif ($homeSection->key === 'jurors') {
            $homeSection->settings = array_merge(
                array_intersect_key($validated, array_flip(self::JURORS_KEYS)),
                ['members' => self::buildJurorRows($validated)]
            );
        }

        if ($homeSection->key) {
            $homeSection->mode = $validated['mode'];
            $homeSection->custom_html = $validated['mode'] === 'custom' ? $validated['custom_html'] : null;
            $homeSection->custom_css = $validated['mode'] === 'custom' ? $validated['custom_css'] : null;
        } else {
            $homeSection->mode = 'custom';
            $homeSection->custom_html = $validated['custom_html'];
            $homeSection->custom_css = $validated['custom_css'];
        }

        $homeSection->label = $validated['label'];
        $homeSection->status = $validated['status'];
        $homeSection->save();

        return redirect()
            ->route('admin.home-sections.index')
            ->with('status', 'Draft saved. Use "View Homepage" to preview it, then Publish to make it live.');
    }

    public function publish(HomeSection $homeSection)
    {
        $homeSection->publish();

        return redirect()
            ->route('admin.home-sections.index')
            ->with('status', 'Section published.');
    }

    public function revert(HomeSection $homeSection)
    {
        $homeSection->revertToPublished();

        return redirect()
            ->route('admin.home-sections.index')
            ->with('status', 'Reverted to the published version.');
    }

    public function destroy(HomeSection $homeSection)
    {
        $homeSection->delete();

        return redirect()
            ->route('admin.home-sections.index')
            ->with('status', 'Bölüm kaldırıldı.');
    }

    public function moveUp(HomeSection $homeSection)
    {
        $previous = HomeSection::where('sort_order', '<', $homeSection->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        $this->swapOrder($homeSection, $previous);

        return redirect()->route('admin.home-sections.index');
    }

    public function moveDown(HomeSection $homeSection)
    {
        $next = HomeSection::where('sort_order', '>', $homeSection->sort_order)
            ->orderBy('sort_order')
            ->first();

        $this->swapOrder($homeSection, $next);

        return redirect()->route('admin.home-sections.index');
    }

    private function swapOrder(HomeSection $a, ?HomeSection $b): void
    {
        if (! $b) {
            return;
        }

        $orderA = $a->sort_order;
        $orderB = $b->sort_order;

        $a->update(['sort_order' => $orderB]);
        $b->update(['sort_order' => $orderA]);
    }

    private static function buildKeyDatesRows(array $input): array
    {
        $rows = [];

        for ($i = 1; $i <= self::KEY_DATES_MAX_ROWS; $i++) {
            $time = trim((string) ($input["date_{$i}_time"] ?? ''));
            $label = trim((string) ($input["date_{$i}_label"] ?? ''));

            if ($time === '' && $label === '') {
                continue;
            }

            $status = trim((string) ($input["date_{$i}_status"] ?? ''));

            $rows[] = [
                'time' => $time,
                'label' => $label,
                'status' => $status !== '' ? $status : 'Scheduled',
            ];
        }

        return $rows;
    }

    private static function buildListItems(array $input, string $prefix, int $max): array
    {
        $items = [];

        for ($i = 1; $i <= $max; $i++) {
            $item = trim((string) ($input["{$prefix}{$i}"] ?? ''));

            if ($item !== '') {
                $items[] = $item;
            }
        }

        return $items;
    }

    private static function buildTitledRows(array $input, string $prefix, int $max): array
    {
        $rows = [];

        for ($i = 1; $i <= $max; $i++) {
            $title = trim((string) ($input["{$prefix}{$i}_title"] ?? ''));
            $text = trim((string) ($input["{$prefix}{$i}_text"] ?? ''));

            if ($title === '' && $text === '') {
                continue;
            }

            $rows[] = ['title' => $title, 'text' => $text];
        }

        return $rows;
    }

    private static function buildJurorRows(array $input): array
    {
        $rows = [];

        for ($i = 1; $i <= self::JURORS_MAX_MEMBERS; $i++) {
            $name = trim((string) ($input["member_{$i}_name"] ?? ''));
            $role = trim((string) ($input["member_{$i}_role"] ?? ''));
            $org = trim((string) ($input["member_{$i}_org"] ?? ''));
            $image = trim((string) ($input["member_{$i}_image"] ?? ''));

            if ($name === '' && $role === '' && $org === '') {
                continue;
            }

            $rows[] = ['name' => $name, 'role' => $role, 'org' => $org, 'image' => $image !== '' ? $image : null];
        }

        return $rows;
    }
}
