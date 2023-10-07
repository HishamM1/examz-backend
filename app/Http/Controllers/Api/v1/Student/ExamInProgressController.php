<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamInProgressController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $id = DB::table('student_exam')->select('exam_id')->where('student_id', $request->user()->student->id)->where('status', 'started')->pluck('exam_id');
        if ($id->isEmpty()) {
            return response()->json(['message' => 'No exam in progress'], 404);
        }
        return response()->json(['exam_id' => $id[0]]);
    }
}
