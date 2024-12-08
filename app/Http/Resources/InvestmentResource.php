<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class InvestmentResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'expected_earning' => $this->user_id,
            'start_date' => $this->method,
            'end_date' => $this->type,
            'status' => $this->status,
            'earning_sum' => $this->earning->sum('amount'),
            'earning' => $this->earning->sum('amount'),
            'package' => $this->package->name,
        ];
    }
}
