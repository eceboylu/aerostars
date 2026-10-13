<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'status' => 'required|boolean',
        ]);

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        $excludedCategoryIds = array_merge(
            [$category->id],
            $category->descendantIds());

        $categories = Category::whereNotIn('id', $excludedCategoryIds)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $forbiddenParentIds = array_merge([$category->id],
            $category->descendantIds());

        $validated = $request->validate([
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn($forbiddenParentIds)],
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,'.$id,
            'status' => 'required|boolean',
        ]);

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return redirect()
            ->route('admin.categories.index');
    }
}
