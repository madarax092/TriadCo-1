<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ReturnedItem;
use App\Models\Report;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category_filter');

        // Fetch returned items with their related items
        $returnedItems = ReturnedItem::with('item')->get();

        // Fetch all item categories
        $categories = ItemCategory::all();

        // Query items with optional filters
        $query = Item::with('category');

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('category', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
        }

        $items = $query->paginate(10);

        // Pass $returnedItems to the view
        return view('inventory.index', compact('items', 'categories', 'returnedItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:110|unique:items,name',
            'category_id' => 'required|exists:item_categories,itemctgry_id',
            'price' => 'required|numeric|min:0',
        ]);

        $item = Item::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
        ]);

        Report::create([
            'activity' => 'Added new item: ' . $item->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('inventory.index')->with('success', 'Item added successfully!');
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $categories = ItemCategory::all();

        return view('inventory.edit', compact('item', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:110|unique:items,name,' . $id . ',item_id',
            'category_id' => 'required|exists:item_categories,itemctgry_id',
            'price' => 'required|numeric|min:0',
        ]);

        $item = Item::findOrFail($id);
        $item->update($validated);

        // Log the activity
        Report::create([
            'activity' => 'Updated item: ' . $item->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('inventory.index')->with('success', 'Item updated successfully!');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        Report::create([
            'activity' => 'Deleted item: ' . $item->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('inventory.index')->with('success', 'Item deleted successfully!');
    }

    public function stockOut($id)
    {
        $returnedItem = ReturnedItem::findOrFail($id);

        $returnedItem->delete();

        return redirect()->route('inventory.index')->with('success', 'Item stocked out successfully!');
    }

    public function reassign($id)
    {
        $returnedItem = ReturnedItem::findOrFail($id);

        $returnedItem->delete();

        return redirect()->route('inventory.index')->with('success', 'Item reassigned successfully!');
    }
}