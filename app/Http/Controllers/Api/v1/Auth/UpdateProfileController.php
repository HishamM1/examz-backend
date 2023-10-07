<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateProfileController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'sometimes', 'string', 'max:255'],
            'email' => ['required', 'sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone_number' => ['required', 'sometimes', 'string', 'max:255', 'unique:users,phone_number,' . $request->user()->id],
            'about' => ['nullable', 'string'],
            'subject' => ['required', 'sometimes', 'string', 'max:255'],
            'school' => ['required', 'sometimes', 'string', 'max:255'],
        ]);

        if ($request->user()->role == "teacher") {
            $request->user()->update($request->except('subject'));

            if ($request->subject) {
                $request->user()->teacher->update($request->only('subject'));
            }
        } else {
            $request->user()->update($request->except('school'));
            if ($request->school) {
                $request->user()->student->update($request->only('school'));
            }
        }

        return response()->json([
            'message' => 'Profile updated successfully',
        ]);
    }
}
