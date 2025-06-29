<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOderController;
use App\Http\Controllers\PO_Product_Controller;
use App\Http\Controllers\PO_Controller;



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
        Route::get('/', [\App\Http\Controllers\GRNController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\GRNController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\GRNController::class, 'store'])->name('store');
        Route::get('/{grn}/edit', [\App\Http\Controllers\GRNController::class, 'edit'])->name('edit');
        Route::put('/{grn}', [\App\Http\Controllers\GRNController::class, 'update'])->name('update');
        Route::delete('/{grn}', [\App\Http\Controllers\GRNController::class, 'destroy'])->name('destroy');
    });


    // Route::prefix('purchase-orders')->group(function () {
    //     Route::get('/', [PO_Controller::class, 'index'])->name('po.index');
    //     Route::get('/create', [PO_Controller::class, 'create'])->name('po.create');
    //     Route::post('/store', [PO_Controller::class, 'store'])->name('po.store');
    //     Route::get('/edit/{id}', [PO_Controller::class, 'edit'])->name('po.edit');
    //     Route::post('/update/{id}', [PO_Controller::class, 'update'])->name('po.update');
    //     Route::delete('/delete/{id}', [PO_Controller::class, 'destroy'])->name('po.delete');

    //     // for temp item storing
    //     Route::post('/add-temp-item', [PO_Controller::class, 'addTempItem'])->name('po.addTempItem');
    //     Route::delete('/remove-temp-item/{index}', [PO_Controller::class, 'removeTempItem'])->name('po.removeTempItem');
    //     Route::get('/get-item-details/{id}', [PO_Controller::class, 'getItemDetails'])->name('po.getItemDetails');
    // });



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

        //PurchaseOder
        Route::get('/purchaseOrder', [PurchaseOderController::class, 'index'])->name('purchaseOrder');

        // Route::get('/add_suppliers', function () {return view('Purchase/suppliers_create'); })->name('suppliers.add');

        Route::get('purchaseOrder/create', [PurchaseOderController::class, 'create'])->name('purchaseOrder.create');

        Route::post('purchaseOrder', [PurchaseOderController::class, 'store'])->name('purchaseOrder.store');

        Route::get('/purchaseOrder/{id}', [PurchaseOderController::class, 'show'])->name('purchaseOrder.show');

        Route::get('/purchaseOrder/{product}/edit', [PurchaseOderController::class, 'edit'])->name('purchaseOrder.edit');

        Route::put('/purchaseOrder/{product}', [PurchaseOderController::class, 'update'])->name('purchaseOrder.update');

        Route::delete('/purchaseOrder/{id}', [PurchaseOderController::class, 'destroy'])->name('purchaseOrder.delete');

        //
        Route::get('/autocomplete-product', [PO_Product_Controller::class, 'autocomplete'])->name('autocomplete.product');
        Route::get('/product-details/{product_id}', [PO_Product_Controller::class, 'getProductDetails'])->name('product.details');
        Route::post('/add-product-line', [PO_Product_Controller::class, 'addProductLine'])->name('add.product.line');
        Route::get('/product-lines', [PO_Product_Controller::class, 'getProductLines'])->name('get.product.lines');

        Route::get('/autocomplete_supplier', [PurchaseOderController::class, 'SupAutocomplete']);
        Route::get('/supplier-details/{supplier}', [PurchaseOderController::class, 'getDetails']);
        Route::post('/create-purchase-order', [PurchaseOderController::class, 'createPurchaseOrder']);
        //



        // Route::get('/suppliers', function () {
        //     return view('Purchase/suppliers');
        // })->name('suppliers');
        // Route::get('/products', function () {
        //     return view('Purchase/products');
        // })->name('products');
        // Route::get('/purchaseOder', function () {
        //     return view('Purchase/purchaseOder');
        // })->name('purchaseOder');
        Route::get('/receivingGRN', function () {
            return view('Purchase/receivingGRN');
        })->name('receivingGRN');
        Route::get('/purchaseReturn', function () {
            return view('Purchase/purchaseReturn');
        })->name('purchaseReturn');

        //Stocks Tab
        Route::get('/currentStock', function () {
            return view('Stock/currentStock');
        })->name('currentStock');
        Route::get('/lowStock', function () {
            return view('Stock/lowStock');
        })->name('lowStock');
        Route::get('/stockAdj', function () {
            return view('Stock/stockAdj');
        })->name('stockAdj');


        //SALes Tab
        Route::get('/saleInvoice', function () {
            return view('Sales/saleInvoice');
        })->name('saleInvoice');
        Route::get('/quotation', action: function () {
            return view(view: 'Sales/Quotation');
        })->name('quotation');
        Route::get('/INVReturn', function () {
            return view(view: 'Sales/INVReturn');
        })->name('INVReturn');
        Route::get('/workOrder', function () {
            return view('Sales/workOrder');
        })->name('workOrder');
        Route::get('/customers', function () {
            return view('Sales/customers');
        })->name('customers');
        Route::get('/vehicles', function () {
            return view('Sales/vehicles');
        })->name('vehicles');
        Route::get('/serviceReminder', function () {
            return view('Sales/serviceReminder');
        })->name('serviceReminder');


        //STAT Tab
        Route::get('/overview', function () {
            return view('Statistics/overview');
        })->name('insights');
        Route::get('/overview', action: function () {
            return view(view: 'Statistics/insights');
        })->name('insights');
        Route::get('/reports', function () {
            return view(view: 'Statistics/reports');
        })->name('reports');

        //BackOffice Tab
        Route::get('/generalSetting', function () {
            return view('genSettting');
        })->name('genSettting');
        Route::get('/staffManagement', action: function () {
            return view(view: 'staffManagement');
        })->name('staffManagement');
        Route::get('/events', function () {
            return view(view: 'events');
        })->name('events');

    });

    // Routes for admins and managers
    Route::middleware('user.type:admin,manager')->group(function () {

    });

    // Routes for users
    Route::middleware('user.type:user')->group(function () {

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
});
