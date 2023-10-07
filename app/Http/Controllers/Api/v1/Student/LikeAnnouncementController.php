<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeAnnouncementController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function like(Request $request, string $id)
    {
        $student = $request->user()->student;
        $likes = DB::table('announcement_likes')->select('student_id','announcement_id')->where('student_id', $student->id)->pluck('announcement_id');

        if (!$likes->contains($id)) {
            $request->user()->student->likes()->create([
                'announcement_id' => $id,
            ]);
        }
    }

    public function dislike(Request $request, string $id) {
        $student = $request->user()->student;
        $likes = DB::table('announcement_likes')->select('student_id','announcement_id')->where('student_id', $student->id)->pluck('announcement_id');

        if ($likes->contains($id)) {
            $student->likes()->where('announcement_id', $id)->delete();
        }
    }
}
