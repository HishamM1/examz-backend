<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        if($user->isTeacher())
        {
            return UserResource::make($user->load('teacher'));
        } else {
            return UserResource::make($user->load('student'));
        }
    }
}
