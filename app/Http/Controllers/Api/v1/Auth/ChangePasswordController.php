<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:2|confirmed',
        ]);

        $user = User::find($request->user()->id);

        if(Hash::check($request->current_password, $user->password)) {
            $request->user()->update([
                'password' => $request->password
            ]);

            return response()->json([
                'message' => 'Password changed successfully'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }
    }
}
