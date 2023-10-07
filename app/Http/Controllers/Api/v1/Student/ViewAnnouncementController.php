<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewAnnouncementController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id)
    {
        $viewed = $request->user()->student->views;
        if (!$viewed->contains($id)) {
            $request->user()->student->views()->create([
                'announcement_id' => $id,
            ]);
        }
        
    }
}
