<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TransactionResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'user_id' => $this->user_id,
            'method' => $this->method,
            'type' => $this->type,
            'group' => $this->group,
            'status' => $this->status,
            'investment' => $this->investment,
            'withdrawal' => $this->withdrawal,
            'earning' => $this->earning,
        ];
    }
}
