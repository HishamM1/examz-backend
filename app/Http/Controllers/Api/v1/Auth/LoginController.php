<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request) {
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $device = substr($request->userAgent() ?? '' , 0, 255);
        $expiresAt = $request->remember ? null : now()->addMinutes(config('session.lifetime'));
        $token = $user->createToken($device, expiresAt: $expiresAt)->plainTextToken;

        return response()->json([
            'access_token' => $token
        ], 201);
    }
}
