<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Item;
use App\Models\Report;
use Illuminate\Http\Request;

class RoomsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roomTypeFilter = $request->input('roomtype_filter');

        $roomTypes = RoomType::all();
        $inventoryItems = Item::all(); // Fetch all inventory items

        $query = Room::with('type');

        if ($roomTypeFilter) {
            $query->where('roomtype_id', $roomTypeFilter);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('type', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
        }

        $rooms = $query->paginate(10);

        return view('rooms.index', compact('rooms', 'roomTypes', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:56|unique:rooms,name',
            'roomtype_id' => 'required|exists:room_types,roomtype_id',
        ]);

        $room = Room::create([
            'name' => $validated['name'],
            'roomtype_id' => $validated['roomtype_id'],
        ]);

        Report::create([
            'activity' => 'Added new room: ' . $room->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room added successfully!');
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $roomTypes = RoomType::all();

        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:56|unique:rooms,name,' . $id . ',room_id',
            'roomtype_id' => 'required|exists:room_types,roomtype_id',
        ]);

        $room = Room::findOrFail($id);
        $room->update($validated);

        Report::create([
            'activity' => 'Updated room: ' . $room->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully!');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        Report::create([
            'activity' => 'Deleted room: ' . $room->name,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully!');
    }

    public function view($id)
    {
        $room = Room::with('type', 'items')->findOrFail($id); 
        $items = Item::all(); 

        return view('rooms.view', compact('room', 'items'));
    }
}