<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PO_Controller;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\JobTypeController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\PaymentCategoryController;
use App\Http\Controllers\PaymentReportController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerVehicleController;
use App\Http\Controllers\CustomerInvoiceController;
use App\Http\Controllers\CustomerServiceHistoryController;
use App\Http\Controllers\CustomerAppointmentController;
use App\Http\Controllers\StaffAppointmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicVehicleController;
use App\Http\Controllers\ServiceScheduleController;
use App\Http\Controllers\InsightsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

Route::get('/popular-items', function () {
    $top = Redis::zrevrange('search_popularity', 0, 10, 'WITHSCORES');
    $data = [];
    foreach ($top as $id => $score) {
        $item = Redis::hget('item_details', $id);
        if ($item) {
            $decoded = json_decode($item, true);
            $decoded['score'] = $score;
            $data[] = $decoded;
        }
    }
    return response()->json($data);
});

// Public vehicle service page (via QR)
Route::get('/v/{vehicleNo}', [PublicVehicleController::class, 'show'])->name('public.vehicle.show');


Route::get('/reset-search-popularity', [PO_Controller::class, 'resetSearchPopularity']);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');



    Route::prefix('purchase-orders')->group(function () {
        // Route::get('/', [PO_Controller::class, 'index'])->name('purchase_orders.index');
        Route::get('/purchase-orders', [PO_Controller::class, 'index'])->name('purchase_orders.index');

        Route::get('/items/search', [PO_Controller::class, 'searchItems'])->name('items.search');
        Route::get('/create', [PO_Controller::class, 'create'])->name('purchase_orders.create');
        Route::post('/store-temp-item', [PO_Controller::class, 'storeTempItem'])->name('purchase_orders.store_temp_item');
        Route::post('/remove-temp-item', [PO_Controller::class, 'removeTempItem'])->name('purchase_orders.remove_temp_item');
        Route::get('/get-item-details/{itemId}', [PO_Controller::class, 'getItemDetails'])->name('purchase_orders.get_item_details');
        Route::post('/store', [PO_Controller::class, 'store'])->name('purchase_orders.store');
        Route::get('/{id}/edit', [PO_Controller::class, 'edit'])->name('purchase_orders.edit');
        Route::get('/purchase-orders/fetch-temp-items', [PO_Controller::class, 'fetchTempItems'])->name('purchase_orders.fetch_temp_items');

        Route::put('/{id}', [PO_Controller::class, 'update'])->name('purchase_orders.update');
        Route::delete('/{id}', [PO_Controller::class, 'destroy'])->name('purchase_orders.destroy');
        Route::get('/purchase-orders/{id}/pdf', [PO_Controller::class, 'exportPdf'])->name('purchase_orders.pdf');
        Route::post('/purchase-orders/{id}/status', [PO_Controller::class, 'changeStatus'])->name('purchase_orders.status');


    });

    Route::prefix('grns')->name('grns.')->group(function () {
        Route::get('/', [GRNController::class, 'index'])->name('index');
        Route::get('/create', [GRNController::class, 'create'])->name('create');
        Route::post('/', [GRNController::class, 'store'])->name('store');
        Route::get('/{grn}/edit', [GRNController::class, 'edit'])->name('edit');
        Route::put('/{grn}', [GRNController::class, 'update'])->name('update');
        Route::delete('/{grn}', [GRNController::class, 'destroy'])->name('destroy');
        Route::get('/{grn}/pdf', [GRNController::class, 'pdf'])->name('pdf');


        Route::post('/store-temp-item', [GRNController::class, 'storeTempItem'])->name('store_temp_item');
        Route::post('/remove-temp-item', [GRNController::class, 'removeTempItem'])->name('remove_temp_item');
        Route::get('/fetch-temp-items', [GRNController::class, 'fetchTempItems'])->name('fetch_temp_items');
        Route::post('/import-from-po', [GRNController::class, 'importFromPO'])->name('import_from_po');

    });

    Route::prefix('purchase-returns')->name('purchase_returns.')->group(function () {
        Route::get('/', [PurchaseReturnController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseReturnController::class, 'create'])->name('create');
        Route::post('/', [PurchaseReturnController::class, 'store'])->name('store');
        Route::delete('/{id}', [PurchaseReturnController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/pdf', [PurchaseReturnController::class, 'pdf'])->name('pdf');
    });

    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');



    // Routes for users and managers
    Route::middleware('user.type:user,manager,admin')->group(function () {

        //PURCHASE Tab
        //Suplier
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');

        // Route::get('/add_suppliers', function () {return view('Purchase/suppliers_create'); })->name('suppliers.add');

        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');

        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');

        Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show');

        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');

        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');

        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.delete');

        //Product
        Route::get('/products', [ProductController::class, 'index'])->name('products');

        // Route::get('/add_suppliers', function () {return view('Purchase/suppliers_create'); })->name('suppliers.add');

        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');

        Route::post('products', [ProductController::class, 'store'])->name('products.store');

        Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.delete');

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        });




        // Vehicle
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [VehicleController::class, 'index'])->name('index');
            Route::get('/create', [VehicleController::class, 'create'])->name('create');
            Route::post('/', [VehicleController::class, 'store'])->name('store');
            Route::get('/{vehicle}', [VehicleController::class, 'show'])->name('show');
            Route::get('/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit');
            Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update');
            Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');
            Route::patch('/{vehicle}/approve', [VehicleController::class, 'approve'])->name('approve');
        });

        Route::get('/api/customers/search', [VehicleController::class, 'customerSearch'])->name('customers.search');
        Route::get('/api/vehicles/check-duplicate', [VehicleController::class, 'checkDuplicate'])->name('vehicles.check_duplicate');

        //JobTypes
        Route::prefix('jobtypes')->name('jobtypes.')->group(function () {
            Route::get('/', [JobTypeController::class, 'index'])->name('index');
            Route::get('/create', [JobTypeController::class, 'create'])->name('create');
            Route::post('/', [JobTypeController::class, 'store'])->name('store');
            Route::get('/{jobtype}', [JobTypeController::class, 'show'])->name('show');
            Route::get('/{jobtype}/edit', [JobTypeController::class, 'edit'])->name('edit');
            Route::put('/{jobtype}', [JobTypeController::class, 'update'])->name('update');
            Route::delete('/{jobtype}', [JobTypeController::class, 'destroy'])->name('destroy');
        });


        //Quatations



        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/', [QuotationController::class, 'index'])->name('index');
            Route::get('/create', [QuotationController::class, 'create'])->name('create');
            Route::post('/', [QuotationController::class, 'store'])->name('store');
            Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])->name('edit');
            Route::put('/{quotation}', [QuotationController::class, 'update'])->name('update');


            Route::post('/temp/add', [QuotationController::class, 'addTempItem'])->name('add_temp_item');
            Route::post('/temp/remove', [QuotationController::class, 'removeTempItem'])->name('remove_temp_item');
            Route::post('/edit-temp/add', [QuotationController::class, 'addEditTempItem'])->name('add_edit_temp_item');
            Route::post('/edit-temp/remove', [QuotationController::class, 'removeEditTempItem'])->name('remove_edit_temp_item');
            Route::get('/edit-temp/items', [QuotationController::class, 'getEditSessionItems'])->name('get_edit_session_items');
            Route::delete('{quotation}/items/{item}', [QuotationController::class, 'removeItem'])->name('remove_item');

            Route::get('/customer-search', [QuotationController::class, 'searchCustomers'])->name('customer_search');
            Route::get('/vehicle-search', [QuotationController::class, 'searchVehicles'])->name('vehicle_search');
            Route::get('/item-search', [QuotationController::class, 'searchItems'])->name('item_search');
            Route::get('/job-search', [QuotationController::class, 'searchJobs'])->name('job_search');
            Route::get('/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('pdf');
            Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->name('destroy');
        });

        // ===== PAYMENT TRANSACTION SYSTEM =====

        // Payment Transactions - Main routes
        Route::prefix('payment-transactions')->name('payment-transactions.')->group(function () {
            Route::get('/', [PaymentTransactionController::class, 'index'])->name('index');
            Route::get('/dashboard', [PaymentTransactionController::class, 'dashboard'])->name('dashboard');
            Route::get('/create', [PaymentTransactionController::class, 'create'])->name('create');
            Route::post('/', [PaymentTransactionController::class, 'store'])->name('store');
            Route::get('/quick-cash-in', [PaymentTransactionController::class, 'quickCashIn'])->name('quick-cash-in');
            Route::get('/quick-cash-out', [PaymentTransactionController::class, 'quickCashOut'])->name('quick-cash-out');

            // AJAX search routes
            Route::get('/search/customers', [PaymentTransactionController::class, 'searchCustomers'])->name('search_customers');
            Route::get('/search/suppliers', [PaymentTransactionController::class, 'searchSuppliers'])->name('search_suppliers');
            Route::get('/search/invoices', [PaymentTransactionController::class, 'searchInvoices'])->name('search_invoices');
            Route::get('/search/purchase-orders', [PaymentTransactionController::class, 'searchPurchaseOrders'])->name('search_purchase_orders');

            // Individual transaction routes
            Route::get('/{paymentTransaction}', [PaymentTransactionController::class, 'show'])->name('show');
            Route::get('/{paymentTransaction}/edit', [PaymentTransactionController::class, 'edit'])->name('edit');
            Route::put('/{paymentTransaction}', [PaymentTransactionController::class, 'update'])->name('update');
            Route::delete('/{paymentTransaction}', [PaymentTransactionController::class, 'destroy'])->name('destroy');

            // Workflow actions
            Route::post('/{paymentTransaction}/approve', [PaymentTransactionController::class, 'approve'])->name('approve');
            Route::post('/{paymentTransaction}/complete', [PaymentTransactionController::class, 'complete'])->name('complete');
            Route::post('/{paymentTransaction}/cancel', [PaymentTransactionController::class, 'cancel'])->name('cancel');
        });

        // Integrated payment creation routes
        Route::post('/sales-invoices/{invoice}/create-payment', [SalesInvoiceController::class, 'createPayment'])->name('sales_invoices.create_payment');
        Route::post('/grns/{grn}/create-payment', [GRNController::class, 'createPayment'])->name('grns.create_payment');
        Route::post('/purchase-returns/{purchase_return}/create-payment', [PurchaseReturnController::class, 'createPayment'])->name('purchase_returns.create_payment');

        // Payment Methods
        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
            Route::get('/create', [PaymentMethodController::class, 'create'])->name('create');
            Route::post('/', [PaymentMethodController::class, 'store'])->name('store');
            Route::get('/{paymentMethod}', [PaymentMethodController::class, 'show'])->name('show');
            Route::get('/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])->name('edit');
            Route::put('/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('update');
            Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('destroy');
            Route::post('/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])->name('toggle_status');
        });

        // Bank Accounts
        Route::prefix('bank-accounts')->name('bank-accounts.')->group(function () {
            Route::get('/', [BankAccountController::class, 'index'])->name('index');
            Route::get('/create', [BankAccountController::class, 'create'])->name('create');
            Route::post('/', [BankAccountController::class, 'store'])->name('store');
            Route::get('/reconcile', [BankAccountController::class, 'reconcileIndex'])->name('reconcileIndex');
            Route::get('/{bankAccount}', [BankAccountController::class, 'show'])->name('show');
            Route::get('/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('edit');
            Route::put('/{bankAccount}', [BankAccountController::class, 'update'])->name('update');
            Route::delete('/{bankAccount}', [BankAccountController::class, 'destroy'])->name('destroy');
            Route::get('/{bankAccount}/statement', [BankAccountController::class, 'statement'])->name('statement');
            Route::get('/{bankAccount}/reconcile', [BankAccountController::class, 'reconcile'])->name('reconcile');
            Route::post('/{bankAccount}/reconcile', [BankAccountController::class, 'processReconciliation'])->name('process_reconciliation');
        });

        // Payment Categories
        Route::prefix('payment-categories')->name('payment-categories.')->group(function () {
            Route::get('/', [PaymentCategoryController::class, 'index'])->name('index');
            Route::get('/create', [PaymentCategoryController::class, 'create'])->name('create');
            Route::post('/', [PaymentCategoryController::class, 'store'])->name('store');
            Route::get('/{paymentCategory}', [PaymentCategoryController::class, 'show'])->name('show');
            Route::get('/{paymentCategory}/edit', [PaymentCategoryController::class, 'edit'])->name('edit');
            Route::put('/{paymentCategory}', [PaymentCategoryController::class, 'update'])->name('update');
            Route::delete('/{paymentCategory}', [PaymentCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/{fromCategory}/merge/{toCategory}', [PaymentCategoryController::class, 'merge'])->name('merge');
        });

        // Payment Reports
        Route::prefix('payment-reports')->name('payment-reports.')->group(function () {
            Route::get('/', [PaymentReportController::class, 'index'])->name('index');
            Route::get('/cash-flow', [PaymentReportController::class, 'cashFlow'])->name('cash_flow');
            Route::get('/category-summary', [PaymentReportController::class, 'categorySummary'])->name('category_summary');
            Route::get('/payment-method-summary', [PaymentReportController::class, 'paymentMethodSummary'])->name('payment_method_summary');
            Route::get('/bank-account-summary', [PaymentReportController::class, 'bankAccountSummary'])->name('bank_account_summary');
            Route::get('/customer-payments', [PaymentReportController::class, 'customerPayments'])->name('customer_payments');
            Route::get('/supplier-payments', [PaymentReportController::class, 'supplierPayments'])->name('supplier_payments');
            Route::get('/outstanding-transactions', [PaymentReportController::class, 'outstandingTransactions'])->name('outstanding_transactions');
            Route::get('/monthly-comparison', [PaymentReportController::class, 'monthlyComparison'])->name('monthly_comparison');
            Route::get('/export', [PaymentReportController::class, 'export'])->name('export');
        });

        // ===== END PAYMENT TRANSACTION SYSTEM =====


        // Route::resource('customers', CustomerController::class);

        // Route::get('/suppliers', function () {
        //     return view('Purchase/suppliers');
        // })->name('suppliers');
        // Route::get('/products', function () {
        //     return view('Purchase/products');
        // })->name('products');
        // Route::get('/purchaseOder', function () {
        //     return view('Purchase/purchaseOder');
        // })->name('purchaseOder');
        // Route::get('/receivingGRN', function () {
        //     return view('Purchase/receivingGRN');
        // })->name('receivingGRN');


        // Route::get('/purchaseReturn', function () {
        //     return view('Purchase/purchaseReturn');
        // })->name('purchaseReturn');

        //Stocks Tab
        // Route::get('/currentStock', function () {
        //     return view('Stock/currentStock');
        // })->name('currentStock');
        Route::get('/lowStock', function () {
            return view('Stock/lowStock');
        })->name('lowStock');



        //SALes Tab
        Route::prefix('sales-invoices')->name('sales_invoices.')->group(function () {
            Route::get('/', [SalesInvoiceController::class, 'index'])->name('index');
            Route::get('/create', [SalesInvoiceController::class, 'create'])->name('create');

            // AJAX routes - These MUST come before wildcard routes
            Route::get('/search/customers', [SalesInvoiceController::class, 'searchCustomers'])->name('search_customers');
            Route::get('/search/items', [SalesInvoiceController::class, 'searchItems'])->name('search_items');
            Route::post('/temp/add', [SalesInvoiceController::class, 'addTempItem'])->name('add_temp_item');
            Route::post('/temp/remove', [SalesInvoiceController::class, 'removeTempItem'])->name('remove_temp_item');
            Route::get('/session-items', [SalesInvoiceController::class, 'getSessionItems'])->name('get_session_items');
            Route::post('/hold', [SalesInvoiceController::class, 'hold'])->name('hold');
            Route::post('/finalize', [SalesInvoiceController::class, 'finalize'])->name('finalize');

            // Wildcard routes - These MUST come after specific routes
            Route::get('/{id}', [SalesInvoiceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [SalesInvoiceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [SalesInvoiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [SalesInvoiceController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/pdf', [SalesInvoiceController::class, 'pdf'])->name('pdf');
            Route::post('/{id}/email', [SalesInvoiceController::class, 'emailInvoice'])->name('email');
            Route::get('/{id}/finalize', [SalesInvoiceController::class, 'finalizeHold'])->name('finalize_hold');
        });

        // Keep old route for compatibility
        Route::get('/saleInvoice', function () {
            return redirect()->route('sales_invoices.index');
        })->name('saleInvoice');
        // Route::get('/quotation', action: function () {
        //     return view(view: 'Sales/Quotation');
        // })->name('quotation');
        // Invoice Returns - Admin/Manager only
        Route::middleware('user.type:admin,manager')->prefix('invoice-returns')->name('invoice_returns.')->group(function () {
            Route::get('/', [App\Http\Controllers\InvoiceReturnController::class, 'index'])->name('index');
            Route::get('/select-invoice', [App\Http\Controllers\InvoiceReturnController::class, 'selectInvoice'])->name('select');
            Route::get('/search/invoices', [App\Http\Controllers\InvoiceReturnController::class, 'searchInvoices'])->name('search_invoices');
            Route::get('/create/{invoice}', [App\Http\Controllers\InvoiceReturnController::class, 'createReturn'])->name('create');
            Route::post('/add-item', [App\Http\Controllers\InvoiceReturnController::class, 'addReturnItem'])->name('add_item');
            Route::post('/remove-item', [App\Http\Controllers\InvoiceReturnController::class, 'removeReturnItem'])->name('remove_item');
            Route::get('/session-items', [App\Http\Controllers\InvoiceReturnController::class, 'getSessionItems'])->name('session_items');
            Route::post('/', [App\Http\Controllers\InvoiceReturnController::class, 'store'])->name('store');
            Route::get('/{return}', [App\Http\Controllers\InvoiceReturnController::class, 'show'])->name('show');
            Route::get('/{return}/pdf', [App\Http\Controllers\InvoiceReturnController::class, 'pdf'])->name('pdf');
        });

        // Keep old route for compatibility
        Route::get('/INVReturn', function () {
            return redirect()->route('invoice_returns.index');
        })->name('INVReturn');

        // Service Invoices
        Route::prefix('service-invoices')->name('service_invoices.')->group(function () {
            Route::get('/', [App\Http\Controllers\ServiceInvoiceController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\ServiceInvoiceController::class, 'create'])->name('create');

            // AJAX routes - These MUST come before wildcard routes
            Route::get('/search/customers', [App\Http\Controllers\ServiceInvoiceController::class, 'customerSearch'])->name('customer_search');
            Route::get('/search/vehicles', [App\Http\Controllers\ServiceInvoiceController::class, 'vehicleSearch'])->name('vehicle_search');
            Route::get('/search/jobs', [App\Http\Controllers\ServiceInvoiceController::class, 'jobSearch'])->name('job_search');
            Route::get('/search/items', [App\Http\Controllers\ServiceInvoiceController::class, 'itemSearch'])->name('item_search');

            // Job item management
            Route::post('/jobs/add', [App\Http\Controllers\ServiceInvoiceController::class, 'addJobItem'])->name('add_job_item');
            Route::post('/jobs/remove', [App\Http\Controllers\ServiceInvoiceController::class, 'removeJobItem'])->name('remove_job_item');
            Route::get('/jobs/session', [App\Http\Controllers\ServiceInvoiceController::class, 'getJobItems'])->name('get_job_items');

            // Spare item management
            Route::post('/spares/add', [App\Http\Controllers\ServiceInvoiceController::class, 'addSpareItem'])->name('add_spare_item');
            Route::post('/spares/remove', [App\Http\Controllers\ServiceInvoiceController::class, 'removeSpareItem'])->name('remove_spare_item');
            Route::get('/spares/session', [App\Http\Controllers\ServiceInvoiceController::class, 'getSpareItems'])->name('get_spare_items');

            // Wildcard routes - These MUST come after specific routes
            Route::post('/', [App\Http\Controllers\ServiceInvoiceController::class, 'store'])->name('store');
            Route::get('/{serviceInvoice}/finalize-options', [App\Http\Controllers\ServiceInvoiceController::class, 'finalizeOptions'])->name('finalize_options');
            Route::get('/{serviceInvoice}', [App\Http\Controllers\ServiceInvoiceController::class, 'show'])->name('show');
            Route::get('/{serviceInvoice}/edit', [App\Http\Controllers\ServiceInvoiceController::class, 'edit'])->name('edit');
            Route::put('/{serviceInvoice}', [App\Http\Controllers\ServiceInvoiceController::class, 'update'])->name('update');
            Route::delete('/{serviceInvoice}', [App\Http\Controllers\ServiceInvoiceController::class, 'destroy'])->name('destroy');
            Route::post('/{serviceInvoice}/finalize', [App\Http\Controllers\ServiceInvoiceController::class, 'finalize'])->name('finalize');
            Route::get('/{serviceInvoice}/add-payment', [App\Http\Controllers\ServiceInvoiceController::class, 'addPayment'])->name('add_payment');
            Route::post('/{serviceInvoice}/store-payment', [App\Http\Controllers\ServiceInvoiceController::class, 'storePayment'])->name('store_payment');
            Route::get('/{serviceInvoice}/pdf', [App\Http\Controllers\ServiceInvoiceController::class, 'pdf'])->name('pdf');
            Route::post('/{serviceInvoice}/email', [App\Http\Controllers\ServiceInvoiceController::class, 'email'])->name('email');
        });

        // Service Schedules (Staff view)
        Route::prefix('service-schedules')->name('service-schedules.')->group(function () {
            Route::get('/', [ServiceScheduleController::class, 'index'])->name('index');
            Route::post('/{vehicleNo}/send', [ServiceScheduleController::class, 'sendReminder'])->name('send');
        });

        // Redirect old workOrder route to service invoices
        Route::get('/workOrder', function () {
            return redirect()->route('service_invoices.index');
        })->name('workOrder');
        // Route::get('/customers', function () {
        //     return view('Sales/customers');
        // })->name('customers');
        // Route::get('/vehicles', function () {
        //     return view('Sales/vehicles');
        // })->name('vehicles');
        Route::get('/serviceReminder', function () {
            return view('Sales/serviceReminder');
        })->name('serviceReminder');


        // STAT Tab - Insights (Admin/Manager only)
        Route::middleware('user.type:admin,manager')->group(function () {
            Route::get('/overview', [InsightsController::class, 'overview'])->name('overview');
            Route::get('/insights', [InsightsController::class, 'index'])->name('insights');
            Route::get('/reports', [InsightsController::class, 'reports'])->name('reports');
            Route::get('/reports/export', [InsightsController::class, 'exportReport'])->name('reports.export');
        });

        //BackOffice Tab
        Route::get('/generalSetting', function () {
            return view('genSettting');
        })->name('genSettting');
        Route::get('/staffManagement', action: function () {
            return view(view: 'staffManagment');
        })->name('staffManagement');
        Route::get('/events', function () {
            return view(view: 'events');
        })->name('events');

    });

    // Routes for admins and managers
    Route::middleware('user.type:admin,manager')->group(function () {

        Route::get('/stockAdj', function () {
            return view('Stock/stockAdj');
        })->name('stockAdj');

    });

    // Routes for users
    Route::middleware('user.type:user')->group(function () {
        // Sales invoices are available to users as well
    });

    // Routes for managers
    Route::middleware('user.type:manager')->group(function () {

    });

    // Routes for admins
    Route::middleware('user.type:admin')->group(function () {

    });

    Route::get('/appointments', function () {
        return view('appointments');
    })->name('appointments');


    Route::get('/403', function () {
        return view('error403');
    })->name('403');

    //Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');

    // Staff Appointment Management
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [StaffAppointmentController::class, 'index'])->name('index');
        Route::get('/calendar', [StaffAppointmentController::class, 'calendar'])->name('calendar');
        Route::get('/calendar/data', [StaffAppointmentController::class, 'getCalendarData'])->name('calendar.data');
        Route::get('/{appointment}', [StaffAppointmentController::class, 'show'])->name('show');
        Route::patch('/{appointment}/confirm', [StaffAppointmentController::class, 'confirm'])->name('confirm');
        Route::patch('/{appointment}/reject', [StaffAppointmentController::class, 'reject'])->name('reject');
        Route::patch('/{appointment}/complete', [StaffAppointmentController::class, 'complete'])->name('complete');
    });

    // Notification Management
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent', [NotificationController::class, 'getRecent'])->name('recent');
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    });
});

// Customer Authentication Routes
Route::prefix('customer')->name('customer.')->group(function () {
    // Guest routes
    Route::middleware('guest.customer')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login']);
        Route::get('/register', [CustomerAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register']);

        // Passwordless login via email OTP
        Route::get('/login/otp', [CustomerAuthController::class, 'showOtpLoginRequestForm'])->name('login.otp.request');
        Route::post('/login/otp', [CustomerAuthController::class, 'sendLoginOtp'])->name('login.otp.send');
        Route::get('/login/otp/verify', [CustomerAuthController::class, 'showLoginOtpForm'])->name('login.otp.form');
        Route::post('/login/otp/verify', [CustomerAuthController::class, 'verifyLoginOtp'])->name('login.otp.verify');
    });

    // Authenticated customer routes (email verification flow)
    Route::middleware('auth.customer')->group(function () {
        // Email verification via OTP
        Route::get('/email/verify', [CustomerAuthController::class, 'showVerificationOtpForm'])->name('verification.otp.form');
        Route::post('/email/verify', [CustomerAuthController::class, 'verifyEmailWithOtp'])->name('verification.otp.verify');
        Route::post('/email/verification-notification', [CustomerAuthController::class, 'resendVerificationOtp'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        // Force change password routes
        Route::get('/password/force-change', [CustomerAuthController::class, 'showForceChangePasswordForm'])->name('password.force.form');
        Route::post('/password/force-change', [CustomerAuthController::class, 'forceChangePassword'])->name('password.force.update');
    });

    // Protected routes (verified customers only) + must change password gate
    Route::middleware(['auth.customer','verified.customer', App\Http\Middleware\EnsureCustomerChangedDefaultPassword::class])->group(function () {
        Route::get('/dashboard', [CustomerAuthController::class, 'dashboard'])->name('dashboard');

                // Vehicle Management Routes
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [CustomerVehicleController::class, 'index'])->name('index');
            Route::get('/create', [CustomerVehicleController::class, 'create'])->name('create');
            Route::get('/check-availability', [CustomerVehicleController::class, 'checkAvailability'])->name('check-availability');
            Route::post('/', [CustomerVehicleController::class, 'store'])->name('store');
            Route::get('/{vehicle}', [CustomerVehicleController::class, 'show'])->name('show');
            Route::get('/{vehicle}/edit', [CustomerVehicleController::class, 'edit'])->name('edit');
            Route::put('/{vehicle}', [CustomerVehicleController::class, 'update'])->name('update');
            Route::get('/{vehicle}/qr', [CustomerVehicleController::class, 'downloadQr'])->name('qr');
        });

        // Invoice Management Routes
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/', [CustomerInvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}', [CustomerInvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/download', [CustomerInvoiceController::class, 'downloadPdf'])->name('download');
        });

        // Service History Routes
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [CustomerServiceHistoryController::class, 'index'])->name('index');
            Route::get('/{serviceInvoice}', [CustomerServiceHistoryController::class, 'show'])->name('show');
            Route::get('/{serviceInvoice}/download', [CustomerServiceHistoryController::class, 'downloadPdf'])->name('download');
        });

        // Appointment Routes
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [CustomerAppointmentController::class, 'index'])->name('index');
            Route::get('/create', [CustomerAppointmentController::class, 'create'])->name('create');
            Route::post('/', [CustomerAppointmentController::class, 'store'])->name('store');
            Route::get('/available-slots', [CustomerAppointmentController::class, 'getAvailableSlots'])->name('available-slots');
            Route::get('/{appointment}', [CustomerAppointmentController::class, 'show'])->name('show');
            Route::patch('/{appointment}/cancel', [CustomerAppointmentController::class, 'cancel'])->name('cancel');
        });
        Route::get('/profile', [CustomerAuthController::class, 'dashboard'])->name('profile');
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });
});

Route::get('/test-redis', function () {
    // Store a value in Redis
    Cache::put('test_redis', 'It works!', 600); // 600 seconds = 10 minutes

    // Retrieve the value
    $value = Cache::get('test_redis');

    return response()->json([
        'redis_value' => $value,
        'status' => $value === 'It works!' ? 'Redis is working ✅' : 'Redis not working ❌',
    ]);
});
