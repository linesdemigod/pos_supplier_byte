<?php

use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DailySaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\MonthlySaleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Report\ItemReportController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Report\SaleReportController;
use App\Http\Controllers\ReturnItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalePointPermissionController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\StoreRequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SwitchBranchController;
use App\Http\Controllers\TimeRestrictionController;
use App\Http\Controllers\TransferOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseInventoryController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'index')->middleware('preventBackToLogin')->name('home');
    Route::post('/', 'authenticate')->name('login');


});

Route::middleware(['auth', 'auth.session'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/home', 'index')->name('dashboard');
        // should be only access by admin
        Route::get('/branches', 'store')->middleware('branchSwitch')->name('dashboard.selection');
        Route::post('/store', 'selectStore')->name('dashboard.store.update');
        Route::post('/warehouse', 'selectWarehouse')->name('dashboard.warehouse.update');

        //get 
        Route::get('stock-level-notification', 'stockLevelNotification');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout')->name('logout');

    });

    Route::controller(ShiftController::class)->prefix('shift')->name('shift.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::post('/store', 'openShift')->name('store');
        Route::put('/update/{shift}', 'closeShift')->name('update');
    });

    Route::controller(ShopController::class)->middleware(['location.access:store'])->name('shop.')->group(function () {
        Route::get('/shop', 'index')->middleware('can:shop.menu')->name('index');
        Route::get('/get-item', 'getItem')->name('items');

        Route::get('/quantity-left', 'getStockLevel')->name('stock.level');
        Route::get('/price-edit', 'priceEdit')->name('price.edit');
        Route::post('/place-order', 'placeOrder')->name('store');
        Route::get('/print-receipt/{id}', 'printReceipt')->name('receipt');
        Route::get('/print-credit-receipt/{id}', 'printCreditReceipt')->name('credit.receipt');

        //hold item
        Route::post('/hold-item', 'holdItem')->name('hold.item');
        Route::get('/release-item', 'releaseItem')->name('release.item');
        Route::get('/fetch-hold-items', 'fetchHoldItems')->name('fetch.item');

    });


    Route::controller(CustomerController::class)->middleware(['location.access:store'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/customer', 'index')->middleware('can:customer.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:customer.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{customer}/edit', 'edit')->middleware('can:customer.edit')->name('edit');
        Route::put('/edit/{customer}', 'update')->name('update');
        Route::delete('/delete/{customer}', 'destroy')->middleware('can:customer.delete')->name('delete');

        Route::get('/get-customer', 'getCustomers')->name('customers');
        //excel
        Route::post('/excel-import', 'excelImport')->middleware('can:customer.import')->name('excel.import');

    });

    Route::controller(DailySaleController::class)->prefix('daily')->name('daily.')->group(function () {
        Route::get('/set-daily', 'setDailySale')->name('set.daily.sale');
        Route::get('/index', 'index')->middleware('can:saleSession.day')->name('index');


    });

    Route::controller(MonthlySaleController::class)->prefix('monthly')->name('monthly.')->group(function () {
        Route::get('/index', 'index')->middleware('can:saleSession.month')->name('index');
        Route::get('/create', 'create')->name('create');

    });

    Route::controller(ItemController::class)->prefix('item')->name('item.')->group(function () {
        Route::get('/index', 'index')->middleware('can:item.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:item.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{item}/edit', 'edit')->middleware('can:item.edit')->name('edit');
        Route::put('/edit/{item}', 'update')->name('update');
        Route::delete('/delete/{item}', 'destroy')->middleware('can:item.delete')->name('delete');
        Route::get('/get-item', 'getItem')->name('items');
        Route::get('/get-warehouse-item', 'getWarehouseItem')->name('items.warehouse');
        Route::get('/price-adjustment', 'priceAdjustment')->middleware('can:priceManagement.menu')->name('price.adjustment');
        Route::post('/price-adjustment-store', 'priceAdjustmentStore')->name('price.adjustment.store');

        //excel\
        Route::post('/excel-import', 'excelImport')->middleware('can:item.import')->name('excel.import');

    });

    Route::controller(CategoryController::class)->prefix('category')->name('category.')->group(function () {
        Route::get('/index', 'index')->middleware('can:category.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:category.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{category}/edit', 'edit')->middleware('can:category.edit')->name('edit');
        Route::put('/edit/{category}', 'update')->name('update');
        Route::delete('/delete/{category}', 'destroy')->middleware('can:category.delete')->name('delete');

        //excel
        Route::post('/excel-import', 'excelImport')->middleware('can:category.import')->name('excel.import');
    });


    Route::controller(StoreController::class)->prefix('store')->name('store.')->group(function () {
        Route::get('/index', 'index')->middleware('can:store.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:store.create')->name('create');
        Route::post('/store', 'store')->name('save');
        Route::get('/edit/{store}/edit', 'edit')->middleware('can:store.edit')->name('edit');
        Route::put('/edit/{store}', 'update')->name('update');
        Route::delete('/delete/{store}', 'destroy')->middleware('can:store.delete')->name('destroy');


    });

    Route::controller(WarehouseController::class)->prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/index', 'index')->middleware('can:warehouse.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:warehouse.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{warehouse}/edit', 'edit')->middleware('can:warehouse.edit')->name('edit');
        Route::put('/edit/{warehouse}', 'update')->name('update');
        Route::delete('/delete/{warehouse}', 'destroy')->middleware('can:warehouse.delete')->name('destroy');


    });

    Route::controller(StoreInventoryController::class)->prefix('store-inventory')->name('storeinventory.')->group(function () {
        Route::get('/index', 'index')->middleware('can:storeInventory.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:storeInventory.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{inventory}/edit', 'edit')->middleware('can:storeInventory.update')->name('edit');
        Route::put('/edit/{inventory}', 'update')->name('update');
        Route::delete('/delete/{inventory}', 'destroy')->middleware('can:storeInventory.delete')->name('destroy');

        //excel
        Route::post('/excel-import', 'excelImport')->middleware('can:storeInventory.import')->name('excel.import');
    });

    Route::controller(WarehouseInventoryController::class)->prefix('warehouse-inventory')->name('warehouseinventory.')->group(function () {
        Route::get('/index', 'index')->middleware('can:warehouseInventory.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:warehouseInventory.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{inventory}/edit', 'edit')->middleware('can:warehouseInventory.update')->name('edit');
        Route::put('/edit/{inventory}', 'update')->name('update');
        Route::delete('/delete/{inventory}', 'destroy')->middleware('can:warehouseInventory.delete')->name('destroy');

        //excel
        Route::post('/excel-import', 'excelImport')->middleware('can:warehouseInventory.import')->name('excel.import');
    });

    Route::controller(SwitchBranchController::class)->prefix('branch')->name('branch.')->group(function () {
        Route::get('/index', 'index')->middleware('can:switchBranch.menu')->name('index');
        Route::put('/update/{id}', 'update')->name('branch-switch.update');
    });

    Route::controller(ItemRequestController::class)->prefix('item-request')->name('itemrequest.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/show/{item}', 'show')->name('show');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{item}/edit', 'edit')->name('edit');
        Route::post('/update', 'update')->name('update');

    });

    Route::controller(StoreRequestController::class)->prefix('store-request')->name('storerequest.')->group(function () {
        Route::get('/index', 'index')->middleware('can:storeRequest.menu')->name('index');
        Route::get('/show/{item}', 'show')->name('show');
        Route::get('/create', 'create')->middleware('can:storeRequest.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{item}/edit', 'edit')->middleware('can:storeRequest.edit')->name('edit');
        Route::post('/update', 'update')->name('update');

    });

    Route::controller(TransferOrderController::class)->prefix('transfer')->name('transfer.')->group(function () {
        Route::get('/index', 'index')->middleware('can:transferOrder.menu')->name('index');
        Route::get('/approval', 'approval')->name('approval');
        Route::get('/cancel', 'cancel')->name('cancel');
        Route::get('/edit/{transfer}/edit', 'edit')->middleware('can:transferOrder.edit')->name('edit');
        Route::put('/update/{transfer}', 'update')->name('update');
        Route::get('/show/{transfer}', 'show')->middleware('can:transferOrder.edit')->name('show');
        Route::post('/dispatch', 'dispatch')->name('dispatch');
        Route::post('/delivered', 'delivered')->name('delivered');

        Route::get('/print/{transfer}', 'print')->name('print');


    });

    Route::controller(UserController::class)->prefix('staff')->name('user.')->group(function () {
        Route::get('/home', 'index')->middleware('can:systemUser.menu')->name('index');
        Route::get('/create', 'create')->middleware('can:systemUser.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{user}/edit', 'edit')->middleware('can:systemUser.edit')->name('edit');
        Route::put('/edit/{user}', 'update')->name('update');
        Route::delete('/user/{user}', 'destroy')->middleware('can:systemUser.delete')->name('destroy');
        Route::post('/account-status', 'account_status')->name('account_status');

        //profile
        Route::get('/profile', 'profile')->name('profile');
        Route::get('/change-password', 'changePassword')->name('change_password');
        Route::put('/update-password', 'updatePassword')->name('update_password');

        //get branchs
        Route::get('/branches', 'getBranchs')->name('getBranchs');
    });

    Route::controller(CreditController::class)->middleware(['can:credit.menu', 'location.access:store'])->prefix('credit')->name('credit.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/summary/{customer}', 'summary')->middleware('can:credit.summary')->name('summary');
        Route::get('/detail/{customer}', 'creditDetail')->middleware('can:credit.detail')->name('detail');
        Route::get('/item-detail/{credit}', 'creditItemDetail')->name('item.detail');

        //repayment of credit by customer
        Route::get('credit-payment', 'creditPaymentDetail')->name('payment.detail');

    });

    Route::controller(ReturnItemController::class)->prefix('return-item')->name('return.')->group(function () {
        Route::get('/show', 'index')->middleware('can:returnItem.menu')->name('index');
        Route::get('/add', 'create')->middleware('can:returnItem.create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{product}/edit', 'edit')->middleware('can:returnItem.edit')->name('edit');
        Route::put('/edit/{product}', 'update')->name('update');
        Route::delete('/show/{product}', 'destroy')->middleware('can:returnItem.delete')->name('destroy');

    });

    Route::controller(CompanyController::class)->middleware('can:company')->prefix('company')->name('company.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{company}/edit', 'edit')->name('edit');
        Route::put('/edit/{company}', 'update')->name('update');
        Route::delete('/company/{company}', 'destroy')->name('destroy');
    });

    Route::controller(SaleController::class)->middleware(['location.access:store'])->prefix('sale')->name('sale.')->group(function () {
        Route::get('/index', 'index')->middleware('can:sale.menu')->name('index');
        Route::get('/show/{sale}', 'show')->middleware('can:sale.show')->name('show');
        Route::get('/print/{id}', 'printReceipt')->middleware('can:sale.print')->name('print');
    });

    Route::controller(ReportController::class)->middleware('can:report')->prefix('report')->name('report.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/employee', 'staff')->name('staff.sales');
        Route::get('/customer', 'customer')->name('customer.index');
        Route::get('/customer-purchase/{customer}', 'purchaseHistory')->name('customer.purchase');
        Route::get('/warehouse', 'warehouse')->name('warehouse');
        Route::get('/shift', 'shift')->name('shift');
    });

    Route::controller(SaleReportController::class)->middleware('can:report')->prefix('report')->name('report.')->group(function () {
        Route::get('/home', 'index')->name('report');
        Route::get('/sales-details', 'summary')->name('summary');
        Route::post('/get-details', 'get_summary')->name('get.summary');
        Route::get('/sale-summary', 'analytics')->name('sale.analytics');
        Route::get('/get-summary', 'get_analytics')->name('sale.get.analytics');
        Route::get('/sales-trends', 'salesTrends')->name('sale.trends');
        Route::get('/item-sales', 'itemSales')->name('sale.items');
        Route::get('/shift-request', 'saleShift')->name('saleShift');
        Route::get('/shift-detail', 'saleShiftDetail')->name('saleShiftDetail');
    });

    Route::controller(ItemReportController::class)->middleware('can:report')->prefix('report')->name('report.')->group(function () {
        Route::get('/stock', 'stock')->name('item.stock');
        Route::get('/price', 'price')->name('item.price');
    });

    Route::controller(SupplierController::class)->middleware(['location.access:store'])->prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        // Route::get('/suppliers', 'suppliers')->name('suppliers');
        Route::get('/purchase', 'purchase')->name('purchase');

        //show
        Route::get('/show/{supplier}', 'show')->name('show');
        Route::get('/detail/{supplier}', 'purchaseDetail')->name('detail');
        Route::get('/purchase-detail/{purchase}', 'purchaseItemDetail')->name('item.detail');

        //payment
        Route::post('/payment', 'payment')->name('payment');
        Route::get('payment-detail', 'paymentDetail')->name('payment.detail');

        //insert purchase
        Route::post('/save-purchase', 'savePurchase')->name('save.purchase');


        //api
        Route::get('/get-supplier', 'getSupplier')->name('get.supplier');

        //search item
        Route::get('/search-item', 'searchItem')->name('search.item');
    });


    Route::controller(TimeRestrictionController::class)->middleware('can:timeRestriction.menu')->prefix('restriction')->name('restriction.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::put('/update/{restriction}', 'update')->name('update');
    });

    Route::controller(AuditTrailController::class)->middleware('can:audit')->prefix('audit')->name('audit.')->group(function () {
        //by date
        Route::get('/date', 'date')->name('date');
        Route::get('/get-date-audit', 'get_date_audit')->name('get_date_audit');
        Route::get('/view-date-audit', 'view_date_audit')->name('view_date_audit');

        //by user
        Route::get('/user', 'user')->name('user');
        Route::get('/get-user-audit', 'get_user_audit')->name('get_user_audit');
        Route::get('/view-user-audit', 'view_user_audit')->name('view_user_audit');

    });

    Route::controller(PermissionController::class)->middleware('can:permissions')->prefix('permission')->name('permission.')->group(function () {
        //permission
        Route::get('/permission', 'allPermission')->name('all.permission');
        Route::get('/get-permission', 'addPermission')->name('add.permission');
        Route::post('/store-permission', 'storePermission')->name('store.permission');

        Route::get('/edit-permission/{permission}/edit', 'editPermission')->name('edit.permission');
        Route::put('/edit-permission/{permission}', 'updatePermission')->name('update.permission');
        //delete
        Route::delete('/permission/{permission}', 'destroy')->name('destroy.permission');

        //roles
        Route::get('/role', 'allRole')->name('all.role');
        Route::get('/get-role', 'addRole')->name('add.role');
        Route::post('/store-role', 'storeRole')->name('store.role');

        Route::get('/edit-role/{role}/edit', 'editRole')->name('edit.role');
        Route::put('/edit-role/{role}', 'updateRole')->name('update.role');
        //delete
        Route::delete('/role/{role}', 'destroyRole')->name('destroy.role');

        //add role permission
        Route::get('/add-role-permission', 'addRolesPermission')->name('add.role.permission');
        Route::post('/role-permission-store', 'rolePermissionStore')->name('role.permission.store');
        Route::get('/all-role-permission', 'allRolesPermission')->name('all.role.permission');
        //edit admin role
        Route::get('/admin-role-permission/{role}/edit', 'adminRolesEdit')->name('admin.edit.role');
        Route::put('/role/update/{role}/edit', 'AdminRolesUpdate')->name('admin.roles.update');
        //delete
        Route::delete('/admin-role-permission/{role}', 'destroyRoleAdmin')->name('admin.destroy.role');
    });

    Route::controller(SalePointPermissionController::class)->middleware('can:permissions')->prefix('permission')->name('permission.')->group(function () {
        Route::get('/sales-point', 'index')->name('sale.point');
        Route::get('/sales-point/{sale}/edit', 'edit')->name('sale.point.edit');
        Route::put('/sales-point/{sale}', 'update')->name('sale.point.update');

    });

});