<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Report;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $suppliers = Supplier::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('address', 'like', "%{$search}%")
                         ->orWhere('number', 'like', "%{$search}%")
                         ->orWhere('contact_person', 'like', "%{$search}%");
        })->get();

        return view('suppliers.index', compact('suppliers'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:56|unique:suppliers,name',
            'address' => 'required|string|max:56|unique:suppliers,address',
            'number' => 'required|string|max:15|unique:suppliers,number',
            'contact_person' => 'required|string|max:56|unique:suppliers,contact_person',
        ]);
        $lastSupplier = Supplier::latest('supplier_id')->first();
        $customId = $lastSupplier ? 'S' . str_pad(intval($lastSupplier->supplier_id) + 1, 3, '0', STR_PAD_LEFT) : 'S001';        
        $supplier = Supplier::create([
            'supplier_id' => $customId,
            ...$validated,
        ]);

        Report::create([
            'activity' => 'Added new supplier: ' . $supplier->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully!');
    }
    public function edit($supplier_id)
    {
        $supplier = Supplier::where('supplier_id', $supplier_id)->firstOrFail();
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $supplier_id)
    {
        $supplier = Supplier::where('supplier_id', $supplier_id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:56|unique:suppliers,name,' . $supplier->supplier_id . ',supplier_id',
            'address' => 'required|string|max:56|unique:suppliers,address,' . $supplier->supplier_id . ',supplier_id',
            'number' => 'required|string|max:15|unique:suppliers,number,' . $supplier->supplier_id . ',supplier_id',
            'contact_person' => 'required|string|max:56|unique:suppliers,contact_person,' . $supplier->supplier_id . ',supplier_id',
        ]);

        $supplier->update($validated);

        Report::create([
            'activity' => 'Updated supplier: ' . $supplier->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
    }
    public function destroy($supplier_id)
    {
        $supplier = Supplier::where('supplier_id', $supplier_id)->firstOrFail();
        $supplierName = $supplier->name;
        $supplier->delete();  

        Report::create([
            'activity' => 'Deleted supplier: ' . $supplierName,
            'user_id' => auth()->id(),
        ]);
        
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully!');
    }
}