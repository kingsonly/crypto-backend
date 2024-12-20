<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="LoginResource",
 *     type="object",
 *     title="Login Resource",
 *     description="A resource representing a successful login response",
 *     @OA\Property(property="status", type="string", example="success", description="The status of the login operation"),
 *     @OA\Property(property="message", type="string", example="Login Successfully", description="The success message"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1, description="The unique identifier of the user"),
 *         @OA\Property(property="name", type="string", example="John Doe", description="The name of the user"),
 *         @OA\Property(property="email", type="string", format="email", example="user@example.com", description="The email address of the user"),
 *         @OA\Property(property="phone", type="string", example="+1234567890", description="The phone number of the user"),
 *         @OA\Property(property="zone", type="string", example="Zone A", description="The zone associated with the user"),
 *         @OA\Property(
 *             property="role",
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=2, description="The unique identifier of the role"),
 *             @OA\Property(property="name", type="string", example="Admin", description="The name of the role")
 *         ),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z", description="The creation timestamp of the user"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T12:00:00Z", description="The last update timestamp of the user")
 *     ),
 *     @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...", description="The JWT token for the authenticated user")
 * )
 */
class LoginResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            "status" => "success",
            "message" => "Login Successfully",
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'is_admin' => $this->is_admin,
            'ref' => $this->ref,
            "token" => $this->createToken("API TOKEN")->plainTextToken
        ];
    }
}
