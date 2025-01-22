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
            'expected_earning' => $this->expected_earning,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'earning_sum' => $this->earning->sum('amount'),
            'earning' => $this->earning,
            'package' => $this->package->name,
        ];
    }
}
