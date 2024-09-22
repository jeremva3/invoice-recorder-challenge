<?php

use App\Http\Controllers\Vouchers\GetTotalAmountsVouchersHandler;
use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\RegularizeStoreVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use App\Http\Controllers\Vouchers\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\Voucher\GetVoucherHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::post('/', StoreVouchersHandler::class);
        Route::put('/regularize', RegularizeStoreVouchersHandler::class);
        Route::get('/totals-amounts', GetTotalAmountsVouchersHandler::class);
        Route::delete('/{id}', DeleteVoucherHandler::class);
    }
);
