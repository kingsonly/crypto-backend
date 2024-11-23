<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="StoreUserResource",
 *     type="object",
 *     title="Store User Resource",
 *     description="Resource representation of a stored user",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the user",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the user",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email address of the user",
 *         example="john.doe@example.com"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the user",
 *         example="+1234567890"
 *     ),
 *     @OA\Property(
 *         property="zone",
 *         type="string",
 *         description="Zone of the user",
 *         example="North Zone"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="object",
 *         description="Role of the user",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="id",
 *                 type="integer",
 *                 description="Role ID",
 *                 example=2
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="Role name",
 *                 example="Admin"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user was created",
 *         example="2024-01-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user was last updated",
 *         example="2024-01-01T12:00:00Z"
 *     )
 * )
 */
class StoreUserResource extends JsonResource
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
            'zone' => $this->zone,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
