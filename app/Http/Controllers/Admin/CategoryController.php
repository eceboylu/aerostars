<?php

namespace App\Http\Controllers\Admin;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
{
    $categories = Category::all();

    return view('admin.categories.index', compact('categories'));
}


public function create()
{
    return view('admin.categories.create');
}


public function store(Request $request)
{
    $validated = $request->validate([
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

    return view('admin.categories.edit', compact('category'));
}


public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
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
