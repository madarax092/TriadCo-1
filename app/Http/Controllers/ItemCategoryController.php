<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categories = ItemCategory::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->get();

        return view('inventory.itemctgry', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(ItemCategory::$rules);

        ItemCategory::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('inventory.itemctgry')->with('success', 'Item category added successfully!');
    }

    public function edit($id)
    {
        $category = ItemCategory::findOrFail($id);
        return view('inventory.itemctgryedit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:110|unique:item_categories,name,' . $id . ',itemctgry_id',
        ]);

        $category = ItemCategory::findOrFail($id);
        $category->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('inventory.itemctgry')->with('success', 'Item category updated successfully!');
    }

    public function destroy($id)
    {
        $category = ItemCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('inventory.itemctgry')->with('success', 'Item category deleted successfully!');
    }
}