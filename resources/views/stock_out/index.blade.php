@extends('dashboard')

@section('title', 'Stock-Out - TriadCo')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2>STOCK-OUT</h2>
    <div class="glass-card glass-card-wide mx-auto">
        <div class="table-responsive mt-2">
            <table class="table table-bordered table-striped align-middle supplier-table">
                <thead class="table-light">
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOutItems as $item)
                        <tr>
                            <td>{{ $item->item->name }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No items in the Stock-Out Cart.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <br>
    <div class="text-end mt-3">
        <form action="{{ route('stock_out.finalize') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-black">Stock Out</button>
        </form>
    </div>
</div>
@endsection