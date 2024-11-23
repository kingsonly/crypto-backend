<?php

namespace App\Service;

use App\Http\Resources\StoreUserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthService
{

    public function Login($request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return false;
        }

        $user = User::where('email', $request->email)->first();
        return $user;
    }

    public function forgotPassword($request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->remember_token = Str::random(60);
            $user->save();
            return $user;
        }
        return false;
    }
}
