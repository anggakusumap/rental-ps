<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use Illuminate\Http\Request;

class FoodCategoryController extends Controller
{
    public function index()
    {
        $categories = FoodCategory::withCount('foodItems')->paginate(20);
        return view('food-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('food-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        FoodCategory::create($validated);

        return redirect()->route('food-categories.index')
            ->with('success', 'Category created successfully');
    }

    public function edit(FoodCategory $foodCategory)
    {
        return view('food-categories.edit', compact('foodCategory'));
    }

    public function update(Request $request, FoodCategory $foodCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $foodCategory->update($validated);

        return redirect()->route('food-categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(FoodCategory $foodCategory)
    {
        if ($foodCategory->foodItems()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing items');
        }

        $foodCategory->delete();

        return redirect()->route('food-categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
