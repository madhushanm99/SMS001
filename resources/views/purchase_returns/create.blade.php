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
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Return Validation:</strong> You can only return items up to the original GRN received quantity. 
                The "Available for Return" column shows how many more can be returned after accounting for previous returns from this GRN.
                Stock availability also applies as a secondary constraint.
            </div>
            <table class="table table-bordered table-sm text-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Item ID</th>
                        <th>Name</th>
                        <th>GRN Received</th>
                        <th>Already Returned</th>
                        <th>Available for Return</th>
                        <th>Stock Available</th>
                        <th>Original Price</th>
                        <th>Discount (%)</th>
                        <th>Unit Price (After Discount)</th>
                        <th>Return Qty</th>
                        <th>Return Value</th>
                        <th>Reason</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="return_items_body"></tbody>
            </table>
        </div>

        <div class="text-right mb-3" id="return_summary" style="display: none;">
            <strong>Total Return Value: Rs. <span id="total_return_value">0.00</span></strong>
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
            const grnData = @json($grn_items_by_grn_id);

            document.getElementById('grn_select').addEventListener('change', function() {
                const grnId = this.value;
                const supplier = this.options[this.selectedIndex]?.dataset.supplier || '';
                document.getElementById('supp_Cus_ID').value = supplier;
                document.getElementById('supplier_display').value = supplier;
                const items = grnData[grnId] || [];
                const tbody = document.getElementById('return_items_body');
                tbody.innerHTML = '';
                
                items.forEach((item, index) => {
                    const maxQty = item.max_returnable_qty;
                    const returnValue = maxQty * item.actual_unit_price;
                    
                    // Skip items that cannot be returned
                    if (maxQty <= 0) {
                        return;
                    }
                    
                    tbody.innerHTML += `
                        <tr data-index="${index}"> 
                            <td> 
                                <input type="hidden" name="items[${index}][item_ID]" value="${item.item_ID}"> 
                                ${item.item_ID} 
                            </td> 
                            <td>
                                ${item.item_Name} 
                                <input type="hidden" name="items[${index}][item_Name]" value="${item.item_Name}"> 
                            </td> 
                            <td>${item.qty_received}</td> 
                            <td class="text-warning">${item.qty_already_returned}</td> 
                            <td class="text-success"><strong>${item.available_for_return}</strong></td> 
                            <td class="text-info">${item.stock_qty}</td> 
                            <td>Rs. ${parseFloat(item.original_price).toFixed(2)}</td> 
                            <td>${parseFloat(item.discount || 0).toFixed(2)}%</td> 
                            <td>Rs. ${parseFloat(item.actual_unit_price).toFixed(2)}</td> 
                            <td> 
                                <input type="number" name="items[${index}][qty_returned]" 
                                       class="form-control form-control-sm return_qty" 
                                       max="${maxQty}" min="1" value="${Math.min(maxQty, 1)}" 
                                       data-unit-price="${item.actual_unit_price}"
                                       data-max-qty="${maxQty}"
                                       data-index="${index}" required> 
                                <input type="hidden" name="items[${index}][actual_unit_price]" value="${item.actual_unit_price}"> 
                                <small class="text-muted">Max: ${maxQty}</small>
                            </td> 
                            <td class="return-value" data-index="${index}">
                                Rs. ${(Math.min(maxQty, 1) * item.actual_unit_price).toFixed(2)}
                            </td>
                            <td> 
                                <input type="text" name="items[${index}][reason]" class="form-control form-control-sm"> 
                            </td> 
                            <td> 
                                <button type="button" class="btn btn-sm btn-danger remove-row">X</button> 
                            </td> 
                        </tr>
                    `;
                });
                
                document.getElementById('grn_item_table_wrapper').classList.remove('d-none');
                document.getElementById('return_summary').style.display = 'block';
                calculateTotalReturnValue();
                attachEventListeners();
            });

            function attachEventListeners() {
                // Remove existing event listeners to avoid duplicates
                document.querySelectorAll('.return_qty').forEach(input => {
                    input.removeEventListener('input', handleQtyChange);
                    input.addEventListener('input', handleQtyChange);
                });

                document.querySelectorAll('.remove-row').forEach(button => {
                    button.removeEventListener('click', handleRemoveRow);
                    button.addEventListener('click', handleRemoveRow);
                });
            }

            function handleQtyChange(event) {
                const input = event.target;
                const index = input.dataset.index;
                const unitPrice = parseFloat(input.dataset.unitPrice);
                const maxQty = parseInt(input.dataset.maxQty);
                let qty = parseInt(input.value) || 0;
                
                // Validate against maximum returnable quantity
                if (qty > maxQty) {
                    alert(`Return quantity cannot exceed ${maxQty} (available for return from this GRN)`);
                    input.value = maxQty;
                    qty = maxQty;
                }
                
                if (qty < 1) {
                    qty = 1;
                    input.value = 1;
                }
                
                const returnValue = qty * unitPrice;
                
                const valueCell = document.querySelector(`.return-value[data-index="${index}"]`);
                if (valueCell) {
                    valueCell.textContent = `Rs. ${returnValue.toFixed(2)}`;
                }
                
                calculateTotalReturnValue();
            }

            function handleRemoveRow(event) {
                event.target.closest('tr').remove();
                calculateTotalReturnValue();
            }

            function calculateTotalReturnValue() {
                let total = 0;
                document.querySelectorAll('.return_qty').forEach(input => {
                    const qty = parseInt(input.value) || 0;
                    const unitPrice = parseFloat(input.dataset.unitPrice) || 0;
                    total += qty * unitPrice;
                });
                
                document.getElementById('total_return_value').textContent = total.toFixed(2);
            }

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
