<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roomTypes = RoomType::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })->get();

        return view('rooms.type', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(RoomType::$rules);

        RoomType::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('rooms.type')->with('success', 'Room type added successfully!');
    }

    public function edit($id)
    {
        $roomType = RoomType::findOrFail($id);
        return view('rooms.typeedit', compact('roomType'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:56|unique:room_types,name,' . $id . ',roomtype_id',
        ]);

        $roomType = RoomType::findOrFail($id);
        $roomType->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('rooms.type')->with('success', 'Room type updated successfully!');
    }

    public function destroy($id)
    {
        $roomType = RoomType::findOrFail($id);
        $roomType->delete();

        return redirect()->route('rooms.type')->with('success', 'Room type deleted successfully!');
    }
}