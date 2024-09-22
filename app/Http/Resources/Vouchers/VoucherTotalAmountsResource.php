<?php

namespace App\Http\Resources\Vouchers;

use App\Http\Resources\Users\UserResource;
use App\Http\Resources\VoucherLines\VoucherLineResource;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherTotalAmountsResource  extends JsonResource
{
    /**
     * @var Voucher
     */
    public $resource;

    public function toArray(Request $request): object
    {
        return (object)[
            'total_soles' => $this->total_soles,
            'total_dollars' => $this->total_dollars,
        ];
    }
}
