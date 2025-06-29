<x-layout title="Dashboard">
    <div class="pagetitle">
        <h1>Purshase Order</h1>




        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Purshase Order</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Purchase Order Details - {{ $purchaseOrder->po_number }}</h2>
                        <div>
                            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Purchase Order Items</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Description</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Line Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($purchaseOrder->items as $item)
                                                    <tr>
                                                        <td>{{ $item->product->name }}</td>
                                                        <td>{{ $item->product->description }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                                        <td>${{ number_format($item->line_total, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No items found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" class="text-end">Total Amount:</th>
                                                    <th>${{ number_format($purchaseOrder->total_amount, 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Purchase Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>PO Number:</strong><br>
                                        {{ $purchaseOrder->po_number }}
                                    </div>

                                    <div class="mb-3">
                                        <strong>Supplier:</strong><br>
                                        {{ $purchaseOrder->supplier->name }}
                                    </div>

                                    <div class="mb-3">
                                        <strong>Order Date:</strong><br>
                                        {{ $purchaseOrder->order_date->format('F d, Y') }}
                                    </div>

                                    <div class="mb-3">
                                        <strong>Status:</strong><br>
                                        <span
                                            class="badge bg-{{ $purchaseOrder->status === 'approved' ? 'success' : ($purchaseOrder->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($purchaseOrder->status) }}
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Total Amount:</strong><br>
                                        <h4 class="text-primary">${{ number_format($purchaseOrder->total_amount, 2) }}
                                        </h4>
                                    </div>

                                    @if ($purchaseOrder->notes)
                                        <div class="mb-3">
                                            <strong>Notes:</strong><br>
                                            {{ $purchaseOrder->notes }}
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <strong>Created:</strong><br>
                                        {{ $purchaseOrder->created_at->format('F d, Y g:i A') }}
                                    </div>

                                    @if ($purchaseOrder->updated_at != $purchaseOrder->created_at)
                                        <div class="mb-3">
                                            <strong>Last Updated:</strong><br>
                                            {{ $purchaseOrder->updated_at->format('F d, Y g:i A') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
