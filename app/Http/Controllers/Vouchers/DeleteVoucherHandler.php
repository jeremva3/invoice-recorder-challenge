<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(string $id): Response
    {
        try {
            $this->voucherService->deleteVoucher($id);

            return response([
                'message' => 'Voucher deleted successfully.',
            ], 200);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
