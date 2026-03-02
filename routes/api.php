<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FeatureController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\TaxController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');



Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/user', 'user');
        Route::get('/auth-status', 'auth_status');
        Route::post('/change-password', 'change_password');
        Route::post('/logout', 'logout');

    });

    Route::controller(ShopController::class)->group(function () {
        Route::get('/get-items', 'getItems');
        Route::get('/category', 'getCategory');
        Route::get('/stock-level', 'getStockLevel');

        //shop items
        Route::get('/shop-items', 'shopItems');

        //discount
        Route::get('/fetch-discount', 'fetchDiscount');

        //place order
        Route::post('/place-order', 'placeOrder');

        //unpaid orders
        Route::get('/unpaid-orders', 'unpaidOrders');

        //payment
        Route::post('/payment', 'payment');

        //order by id
        Route::get('/orders', 'fetchOrderById');

        Route::get('/fetch-order', 'fetchOrder');

        //mobile items
        Route::get('/fetch-items', 'fetchItems');

    });


    Route::controller(CustomerController::class)->group(function () {
        Route::get('/get-customers', 'index');
        Route::get('/search-customer', 'show');
        Route::post('/create-customer', 'create');
        Route::put('/update-customer/{id}', 'update');
        Route::delete('/delete-customer/{id}', 'destroy');


    });

    Route::controller(FeatureController::class)->group(function () {
        Route::get('/get-feature', 'index');
    });

    Route::controller(TaxController::class)->group(function () {
        Route::get('/order-tax', 'index');
    });

    Route::controller(CompanyController::class)->group(function () {
        Route::get('/company', 'index');
    });

    Route::controller(SaleController::class)->group(function () {
        Route::get('/get-sales', 'index');
    });

    Route::controller(CreditController::class)->group(function () {
        Route::get('/customer-credits', 'customerCredits');
        Route::get('/customer-credit/{customer}', 'show');

        Route::get('/credit-detail', 'creditDetail');
        Route::get('/credit-item-detail/{credit}', 'creditItemDetail');


        //repayment of credit by customer
        Route::get('credit-payment', 'creditPaymentDetail');

        //void
        Route::put('credit-void/{credit}', 'voidCredit');


        Route::get('/repayment', 'repayment');
        Route::get('/fetch-repayment', 'fetchRepayment');
        Route::post('/pay-repayment', 'payRepayment');


    });

});
