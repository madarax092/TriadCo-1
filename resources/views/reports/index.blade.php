@extends('dashboard')

@section('title', 'Reports - Recent Activities')

@section('head')
    <link href="{{ asset('css/supplier.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-5 text-primary">REPORTS</h2>

    <!-- Statistics Cards -->
    <div class="stats-card">
        <div class="card">
            <h3>{{ $totalStockInThisMonth }}</h3>
            <p>Total Stock-In This Month</p>
        </div>
        <div class="card">
            <h3>{{ $totalStockOutThisMonth }}</h3>
            <p>Total Stock-Out This Month</p>
        </div>
        <div class="card">
            <h3>{{ $totalSuppliers }}</h3>
            <p>Total Suppliers</p>
        </div>
        <div class="card">
            <h3>{{ $totalItems }}</h3>
            <p>Total Items</p>
        </div>
    </div>

    <div class="glass-card glass-card-wide mx-auto">
        <!-- Reports Table -->
        <div class="table-responsive mt-2">
            <table class="table table-bordered table-striped align-middle supplier-table">
                <thead>
                    <tr>
                        <td colspan="3" class="text-center">
                            <form action="{{ route('reports.index') }}" method="GET" class="d-flex justify-content-center align-items-center">
                                <label for="filter" class="me-2 fw-bold filter-label">Filter Reports:</label>
                                <select name="filter" id="filter" class="form-select form-select-sm filter-dropdown" onchange="this.form.submit()">
                                    <option value="" {{ request('filter') == '' ? 'selected' : '' }}>All Reports</option>
                                    <option value="items" {{ request('filter') == 'items' ? 'selected' : '' }}>Items Reports</option>
                                    <option value="suppliers" {{ request('filter') == 'suppliers' ? 'selected' : '' }}>Supplier Reports</option>
                                    <option value="stock_in" {{ request('filter') == 'stock_in' ? 'selected' : '' }}>Stock-In Reports</option>
                                    <option value="stock_out" {{ request('filter') == 'stock_out' ? 'selected' : '' }}>Stock-Out Reports</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                </thead>
                <thead class="table-light">
                    <tr>
                        <th>Activity</th>
                        <th>User</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->activity }}</td>
                            <td>{{ $report->user ? $report->user->name : 'Unknown User' }}</td>
                            <td>{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No recent activities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection