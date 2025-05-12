@extends('dashboard')

@section('title', 'Rooms - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2>ROOMS</h2>
    <div class="mb-4 d-flex gap-2">
        <button class="btn btn-add-supplier w-50" onclick="toggleModal('addRoomModal', 'open')">
            Add Room
        </button>
        <button onclick="window.location.href='{{ route('rooms.type') }}'" class="btn btn-page-link w-50 text-center">
            Room Types
        </button>
    </div>

    <!-- Modal -->
    <div class="supplier-modal hidden" id="addRoomModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Add Room
            </div>
            <div class="modal-body">
                <form action="{{ route('rooms.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Room Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter room name" required>
                    </div>
                    <div class="mb-3">
                        <label for="roomtype_id" class="form-label">Room Type</label>
                        <select class="form-control" id="roomtype_id" name="roomtype_id" required>
                            <option value="" disabled selected>Select a room type</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->roomtype_id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn-add">Add Room</button>
                        <button type="button" class="btn-cancel" onclick="toggleModal('addRoomModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Items Modal -->
    <div class="supplier-modal hidden" id="assignItemsModal">
        <div class="modal-content">
            <div class="supplier-modal-header">
                Assign Items to this Room
            </div>
            <div class="modal-body">
                <form id="assign-items-form" action="{{ route('rooms.assign', ['id' => '__ROOM_ID__']) }}" method="POST">
                    @csrf
                    <div id="items-container">
                        <!-- First Row -->
                        <div class="item-row">
                            <label for="item_id_0" class="assign-item-form-label">Item:</label>
                            <select name="items[0][item_id]" id="item_id_0" class="form-select item-select" required>
                                <option value="" disabled selected>Select Item</option>
                                @foreach($inventoryItems as $item)
                                    <option value="{{ $item->item_id }}" data-stock="{{ $item->in_stock }}">
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="quantity_0" class="assign-item-form-label">Qty:</label>
                            <input type="number" name="items[0][quantity]" id="quantity_0" class="form-control quantity-input" min="1" placeholder="Qty" required disabled>
                            <button type="button" class="btn btn-square btn-add-row">+</button>
                        </div>
                    </div>
                    <!-- Confirm and Cancel Buttons -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Confirm</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleModal('assignItemsModal', 'close')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rooms Table -->
    <div class="glass-card glass-card-wide mx-auto">
        <div class="table-responsive mt-2">
            <table class="table table-bordered table-striped align-middle supplier-table">
                <thead class="table-light">
                    <tr>
                        <td colspan="6">
                            <form action="{{ route('rooms.index') }}" method="GET" class="d-flex justify-content-end">
                                <label for="roomtype_filter" class="me-2 fw-bold filter-label">Room Filter:</label>
                                <select name="roomtype_filter" id="roomtype_filter" class="form-select form-select-sm filter-dropdown me-3" onchange="this.form.submit()">
                                    <option value="" {{ request('roomtype_filter') == '' ? 'selected' : '' }}>All Room Types</option>
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type->roomtype_id }}" {{ request('roomtype_filter') == $type->roomtype_id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search rooms..." value="{{ request('search') }}" />
                                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <th>Room ID</th>
                        <th>Room Name</th>
                        <th>Room Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>{{ $room->room_id }}</td>
                            <td>{{ $room->name }}</td>
                            <td>{{ $room->type->name }}</td>
                            <td>
                                @if ($room->status === 'occupied')
                                    <span class="badge badge-occupied">Occupied</span>
                                @else
                                    <span class="badge badge-empty">Empty</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-assign" onclick="toggleAssignItemsModal('{{ $room->room_id }}')">Assign Items</button>
                                    <a href="{{ route('rooms.view', $room->room_id) }}" class="btn btn-sm btn-violet">View Room</a>
                                </div>            
                            </td> 
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No rooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection