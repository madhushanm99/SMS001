<x-layout> <x-slot name="title">GRN List</x-slot>
    <div class="mb-4 d-flex justify-content-between">
        <h2 class="h4">Goods Received Notes</h2> <a href="{{ route('grns.create') }}" class="btn btn-primary">+ New
            GRN</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-sm text-sm">
            <thead class="thead-light">
                <tr>
                    <th>GRN No</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>PO No</th>
                    <th>Invoice No</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grns as $grn)
                    <tr>
                        <td>{{ $grn->grn_no }}</td>
                        <td>{{ $grn->grn_date }}</td>
                        <td>{{ $grn->supp_Cus_ID }}</td>
                        <td>{{ $grn->po_No ?? '-' }}</td>
                        <td>{{ $grn->invoice_no ?? '-' }}</td>
                        {{-- <td>
                            @if ($grn->status)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Deleted</span>
                            @endif
                        </td> --}}
                        <td class="text-center">
                            @if ($grn->status)
                                <a href="{{ route('grns.pdf', $grn->grn_id) }}" target="_blank"
                                    class="btn btn-sm btn-outline-secondary">Print</a>
                                <a href="{{ route('grns.edit', $grn->grn_id) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('grns.destroy', $grn->grn_id) }}" method="POST"
                                    class="d-inline-block delete-form"> @csrf @method('DELETE') <button type="submit"
                                        class="btn btn-sm btn-danger">Delete</button> </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                </tr> @empty <tr>
                        <td colspan="7" class="text-center text-muted">No GRNs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $grns->links() }}
    </div>

    <!-- Include Payment Prompt Modal -->
    <x-payment-prompt type="grn" payment_type="cash_out" title="Record Supplier Payment" />

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', e => {
                    if (!confirm('Are you sure you want to delete this GRN?')) e.preventDefault();
                });
            });

            // Check for GRN creation flash data and show payment prompt
            @if(session('grn_created'))
                $(document).ready(function() {
                    const grnData = @json(session('grn_created'));
                    
                    if (grnData.prompt_payment) {
                        // Show payment prompt modal
                        showPaymentPrompt({
                            type: 'grn',
                            entity_id: grnData.grn_id,
                            entity_no: grnData.grn_no,
                            party_name: grnData.supplier_name,
                            total_amount: grnData.total_amount,
                            outstanding_amount: grnData.total_amount
                        });
                    }
                });
            @endif
        </script>
    @endpush
</x-layout>
