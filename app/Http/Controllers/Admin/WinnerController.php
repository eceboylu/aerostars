<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class WinnerController extends Controller
{
    public function index()
    {
        $winners = Winner::with('category.parent')
            ->orderByDesc('won_at')
            ->get();

        return view('admin.winners.index', compact('winners'));
    }

    public function create()
    {
        $families = $this->families();

        return view('admin.winners.create', compact('families'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if (empty($validated['verification_slug'])) {
            $category = Page::find($validated['category_page_id']);

            $validated['verification_slug'] = Winner::makeVerificationSlug(
                $validated['company_name'],
                $category->title,
                Carbon::parse($validated['won_at'])->year
            );
        }

        $winner = Winner::create($validated);

        $this->saveCategoryBadge($request, $validated['category_page_id']);

        return redirect()
            ->route('admin.winners.edit', $winner)
            ->with('status', 'Kazanan eklendi. Firmaya verilecek rozet ve embed kodu aşağıda hazır.');
    }

    public function edit(Winner $winner)
    {
        $families = $this->families();

        return view('admin.winners.edit', compact('winner', 'families'));
    }

    public function update(Request $request, Winner $winner)
    {
        $validated = $this->validated($request, $winner);

        $winner->update($validated);

        $this->saveCategoryBadge($request, $validated['category_page_id']);

        return redirect()
            ->route('admin.winners.index');
    }

    public function destroy(Winner $winner)
    {
        $winner->delete();

        return redirect()
            ->route('admin.winners.index');
    }

    private function validated(Request $request, ?Winner $winner = null): array
    {
        $slugRule = $winner ? 'required' : 'nullable';

        return $request->validate([
            'category_page_id' => 'required|exists:pages,id',
            'company_name' => 'required|string|max:255',
            'verification_slug' => [
                $slugRule,
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('winners', 'verification_slug')->ignore($winner?->id),
            ],
            'logo' => 'nullable|string|max:2048',
            'description' => 'nullable|string|max:2000',
            'won_at' => 'required|date',
            'status' => 'required|boolean',
            'category_badge_image' => 'nullable|string|max:2048',
        ]);
    }

    private function saveCategoryBadge(Request $request, int $categoryPageId): void
    {
        Page::whereKey($categoryPageId)->update([
            'badge_image' => $request->input('category_badge_image'),
        ]);
    }

    private function families()
    {
        $categoriesRoot = Page::where('slug', 'categories')->first();

        if (! $categoriesRoot) {
            return collect();
        }

        return $categoriesRoot->children()
            ->with(['children' => fn ($query) => $query->orderBy('title')])
            ->orderBy('title')
            ->get();
    }
}
