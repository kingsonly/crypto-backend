<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ShowUserResource.
 *
 *
 * @OA\Schema(
 *     schema="ShowUserResource",
 *     type="object",
 *     title="Show Staff Resource",
 *     description="Show Staff Resource",
 * )
 */
class ShowUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_admin' => $this->is_admin,
            'username' => $this->username,
            'ref' => $this->ref,
        ];
    }
}
