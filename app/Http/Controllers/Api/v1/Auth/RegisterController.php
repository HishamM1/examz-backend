<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request) {
        $user = User::create($request->validated());
        
        if($user->role == 'teacher') {
            $join_code = substr(md5(time() . $user->id), 0, 6);
                
            $user->teacher()->create(
                [
                'subject' => $request->subject,
                'join_code' => $join_code
                ]
            );
        } else {
            $user->student()->create([
                'school' => $request->school
            ]);
        }

        event(new Registered($user));

        Auth::login($user);
        
        $device = substr($request->userAgent() ?? '' , 0, 255);

        return response()->json([
            'token' => $user->createToken($device)->plainTextToken,
        ], 201);
    }
}
