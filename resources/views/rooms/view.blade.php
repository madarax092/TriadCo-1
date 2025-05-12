<!-- filepath: c:\Users\jarma\Documents\TriadCo\TriadCo\resources\views\rooms\view.blade.php -->
@extends('dashboard')

@section('title', 'View Room - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <!-- Action Buttons -->
    <div class="mb-4 d-flex gap-2">
        <button class="btn btn-major-edit w-40" onclick="window.location.href='{{ route('rooms.edit', $room->room_id) }}'">
            Edit Room Information
        </button>
        <button class="btn btn-major-delete w-40" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this room?')) document.getElementById('delete-room-form').submit();">
            Delete Room
        </button>
        <form id="delete-room-form" action="{{ route('rooms.destroy', $room->room_id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        <form action="{{ route('rooms.index') }}" method="GET" class="w-20">
            <button type="submit" class="btn btn-back text-center">
                Back
            </button>
        </form>
    </div>

    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">{{ $room->room_id }} - {{ $room->name }}</h2>
    </div>

    <div class="glass-card glass-card-wide mx-auto">
        <div class="table-responsive mt-2">
            <table class="table table-bordered table-striped align-middle supplier-table">
                <thead class="table-light">
                    <tr>
                        <td colspan="6">
                            <form action="{{ route('rooms.view', $room->room_id) }}" method="GET" class="d-flex justify-content-end">
                                <label for="item_filter" class="me-2 fw-bold filter-label">Item Filter:</label>
                                <select name="item_filter" id="item_filter" class="form-select form-select-sm filter-dropdown me-3" onchange="this.form.submit()">
                                    <option value="" {{ request('item_filter') == '' ? 'selected' : '' }}>All Items</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->item_id }}" {{ request('item_filter') == $item->item_id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search items..." value="{{ request('search') }}" />
                                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($room->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->pivot->quantity }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" onclick="openReturnItemModal('{{ $item->item_id }}', {{ $item->pivot->quantity }})">
                                    Return Item
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No items assigned to this room.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Return Item Modal -->
<div class="supplier-modal hidden" id="returnItemModal">
    <div class="modal-content">
        <div class="supplier-modal-header">
            Return Item
        </div>
        <div class="modal-body">
            <form action="{{ route('rooms.returnItem', ['id' => $room->room_id]) }}" method="POST">
                @csrf
                <input type="hidden" id="return_item_id" name="item_id">
                <div class="mb-3">
                    <label for="return_quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="return_quantity" name="quantity" min="1" placeholder="Enter quantity to return" required>
                </div>
                <div class="mb-3">
                    <label for="return_reason" class="form-label">Reason</label>
                    <textarea class="form-control" id="return_reason" name="reason" rows="3" placeholder="Enter reason for return" required></textarea>
                </div>
                <div class="button-row">
                    <button type="submit" class="btn-add">Confirm</button>
                    <button type="button" class="btn-cancel" onclick="toggleModal('returnItemModal', 'close')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection