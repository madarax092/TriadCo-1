<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Item;
use App\Models\Report;
use Illuminate\Http\Request;

class StockOutController extends Controller
{
    public function index()
    {
        $stockOutItems = StockOut::with('item')->get();

        return view('stock_out.index', compact('stockOutItems'));
    }

    public function addToStockOut(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($id);

        if ($validated['quantity'] > $item->in_stock) {
            return redirect()->back()->withErrors(['error' => 'Quantity exceeds available stock.']);
        }

        StockOut::create([
            'item_id' => $item->item_id,
            'quantity' => $validated['quantity'],
        ]);

        $item->in_stock -= $validated['quantity'];
        $item->save();

        return redirect()->route('stock_out.index')->with('success', 'Item added to Stock-Out successfully.');
    }

    public function finalize(Request $request)
    {
        $stockOutItems = StockOut::with('item')->get();

        foreach ($stockOutItems as $item) {
            Report::create([
                'activity' => 'Stocked out item: ' . $item->item->name . ' (Quantity: ' . $item->quantity . ')',
                'user_id' => auth()->id(),
            ]);
        }

        StockOut::truncate();

        return redirect()->route('stock_out.index')->with('success', 'All items have been stocked out.');
    }
}