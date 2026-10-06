<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('category')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.pages.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        Page::create($validated);

        return redirect()
            ->route('admin.pages.index');
    }

    public function edit($id)
{
    $page = Page::findOrFail($id);

    $categories = Category::all();

    return view('admin.pages.edit', compact('page', 'categories'));
}

public function update(Request $request, $id)
{
    $page = Page::findOrFail($id);

    $validated = $request->validate([
        'category_id' => 'nullable|exists:categories,id',
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:pages,slug,' . $id,
        'content' => 'nullable|string',
        'status' => 'required|boolean',
    ]);

    $page->update($validated);

    return redirect()
        ->route('admin.pages.index');
}

public function destroy($id)
{
    $page = Page::findOrFail($id);

    $page->delete();

    return redirect()
        ->route('admin.pages.index');
}

}
