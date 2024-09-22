<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Http\Resources\Vouchers\VoucherTotalAmountsResource;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetTotalAmountsVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService) {}

    public function __invoke(): Response
    {
        try {
            $totals = $this->voucherService->getTotalAmounts();
            return response([
                'data' => new  VoucherTotalAmountsResource($totals),
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
