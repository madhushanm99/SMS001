<x-layout> <x-slot name="title">Purchase Return</x-slot>
    <form method="POST" action="{{ route('purchase_returns.store') }}" id="pr_form"> @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="grn_select">Select GRN</label>
                <select id="grn_select" class="form-control" name="grn_id" required>
                    <option value="">-- Choose GRN --</option>
                    @foreach ($grns as $grn)
                        <option value="{{ $grn->grn_id }}" data-supplier="{{ $grn->supp_Cus_ID }}">{{ $grn->grn_no }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Supplier</label>
                <input type="text" class="form-control" id="supplier_display" disabled>
                <input type="hidden" name="supp_Cus_ID" id="supp_Cus_ID">
            </div>
        </div>

        <div id="grn_item_table_wrapper" class="d-none">
            <table class="table table-bordered table-sm text-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Item ID</th>
                        <th>Name</th>
                        <th>Received</th>
                        <th>Available</th>
                        <th>Return Qty</th>
                        <th>Reason</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="return_items_body"></tbody>
            </table>
        </div>

        <div class="form-group mt-3">
            <label for="note">General Note</label>
            <textarea name="note" id="note" class="form-control"></textarea>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary" id="submit-return-btn">Submit Return</button>
        </div>
    </form>

    <!-- Payment Prompt Modal -->
    @include('components.payment-prompt', [
        'type' => 'purchase_return',
        'payment_type' => 'cash_in',
        'payment_methods' => [],
        'bank_accounts' => [],
        'payment_categories' => []
    ])
    @push('scripts')
        <script>
            const grnData =
                @json($grn_items_by_grn_id);

            document.getElementById('grn_select').addEventListener('change', function() {
                const grnId = this.value;
                const supplier = this.options[this.selectedIndex]?.dataset.supplier || '';
                document.getElementById('supp_Cus_ID').value = supplier;
                document.getElementById('supplier_display').value = supplier;
                const items = grnData[grnId] || [];
                const tbody = document.getElementById('return_items_body');
                tbody.innerHTML = '';
                items.forEach((item, index) => {
                    const maxQty = item.stock_qty >= item.qty_received ? item.qty_received : item.stock_qty;
                    tbody.innerHTML +=
                        ` <tr data-index="${index}"> <td> <input type="hidden" name="items[${index}][item_ID]" value="${item.item_ID}"> ${item.item_ID} </td> <td>${item.item_Name} <input type="hidden" name="items[${index}][item_Name]" value="${item.item_Name}"> </td> <td>${item.qty_received}</td> <td>${item.stock_qty}</td> <td> <input type="number" name="items[${index}][qty_returned]" class="form-control form-control-sm return_qty" max="${maxQty}" min="1" value="${maxQty}" required> <input type="hidden" name="items[${index}][price]" value="${item.price}"> </td> <td> <input type="text" name="items[${index}][reason]" class="form-control form-control-sm"> </td> <td> <button type="button" class="btn btn-sm btn-danger remove-row">X</button> </td> </tr> `;
                });
                document.getElementById('grn_item_table_wrapper').classList.remove('d-none');
            });
            document.getElementById('return_items_body').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
            document.getElementById('return_items_body').addEventListener('input', function(e) {
                if (e.target.classList.contains('return_qty')) {
                    const max = parseInt(e.target.getAttribute('max'), 10);
                    const val = parseInt(e.target.value, 10);
                    if (val > max) {
                        alert('Return quantity cannot exceed available stock.');
                        e.target.value = max;
                    }
                }
            });

            // Set global entity type for payment modal
            window.currentEntityType = 'purchase_return';

            // Global variables
            let currentPurchaseReturnId = null;
            let currentOutstandingAmount = 0;

            // Handle form submission with payment prompt
            document.getElementById('pr_form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('show_payment_prompt', '1'); // Request payment prompt
                
                const submitBtn = document.getElementById('submit-return-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Creating...';
                
                fetch('{{ route("purchase_returns.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.show_payment_prompt) {
                        // Store purchase return data
                        currentPurchaseReturnId = data.purchase_return_id;
                        currentOutstandingAmount = parseFloat(data.total_amount) || 0;
                        
                        // Populate payment modal
                        const totalAmount = parseFloat(data.total_amount) || 0;
                        document.getElementById('modal-entity-no').textContent = data.return_no;
                        document.getElementById('modal-party-name').textContent = data.supplier_name;
                        document.getElementById('modal-total-amount').textContent = totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2});
                        document.getElementById('modal-outstanding-amount').textContent = totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2});
                        
                        // Set payment amount to full amount
                        document.getElementById('payment_amount').value = totalAmount.toFixed(2);
                        document.getElementById('payment_amount').setAttribute('max', totalAmount);
                        
                        // Load payment methods
                        populatePaymentMethods(data.payment_methods);
                        populateBankAccounts(data.bank_accounts);
                        populatePaymentCategories(data.payment_categories);
                        
                        // Show payment modal
                        $('#paymentPromptModal').modal('show');
                    } else {
                        // Handle error or redirect
                        window.location.href = '{{ route("purchase_returns.index") }}';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while creating the purchase return.', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Return';
                });
            });

            function populatePaymentMethods(methods) {
                const select = document.getElementById('payment_method_id');
                select.innerHTML = '<option value="">-- Select Payment Method --</option>';
                methods.forEach(method => {
                    select.innerHTML += `<option value="${method.id}">${method.name}</option>`;
                });
            }

            function populateBankAccounts(accounts) {
                const select = document.getElementById('bank_account_id');
                select.innerHTML = '<option value="">-- Select Bank Account --</option>';
                accounts.forEach(account => {
                    select.innerHTML += `<option value="${account.id}">${account.account_name} - ${account.account_number}</option>`;
                });
            }

            function populatePaymentCategories(categories) {
                const select = document.getElementById('payment_category_id');
                select.innerHTML = '<option value="">-- Select Category --</option>';
                categories.forEach(category => {
                    select.innerHTML += `<option value="${category.id}">${category.description || category.name}</option>`;
                });
            }

            function recordPayment() {
                const formData = new FormData(document.getElementById('paymentForm'));
                const submitBtn = document.getElementById('recordPaymentBtn');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';
                
                fetch(`/purchase-returns/${currentPurchaseReturnId}/create-payment`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#paymentPromptModal').modal('hide');
                        
                        Swal.fire({
                            title: 'Success!',
                            text: 'Purchase return created and refund recorded successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route("purchase_returns.index") }}';
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Refund error:', error);
                    Swal.fire('Error', 'An error occurred while processing the refund.', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-cash-coin me-1"></i>Record Refund';
                });
            }

            function skipPayment() {
                $('#paymentPromptModal').modal('hide');
                Swal.fire({
                    title: 'Purchase Return Created!',
                    text: 'Purchase return has been created successfully. Refund can be recorded later.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '{{ route("purchase_returns.index") }}';
                });
            }
        </script>
    @endpush
</x-layout>
