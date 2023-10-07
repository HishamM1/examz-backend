<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamAndQuestionsResource;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;

class StartExamController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Exam $exam)
    {


        $student = $exam->students()
            ->where('student_id', $request->user()->student->id)
            ->first();

        $exam_ended = $exam->end_time && $exam->end_time < now();

        $student_joined_teacher = $request->user()->student->teachers()
            ->where('teacher_id', $exam->teacher->id)
            ->exists();
            
        $student_duration_passed = $student
            ? $student->pivot->created_at->addMinutes($exam->duration) < now()
            : false;
            
        $student_finished_exam = $student && $student->pivot->status === 'finished';

        $already_in_another_exam = DB::table('student_exam')->where('student_id', $request->user()->student->id)
            ->where('status', 'started')
            ->where('exam_id', '!=', $exam->id)
            ->exists();

        if ($already_in_another_exam) {
            return response()->json([
                'message' => 'You are already in another exam',
                'status' => "exam_in_progress"
            ], 401);
        }

        if (!$student_joined_teacher || !$exam->visible || $exam_ended) {
            return response()->json([
                'message' => 'You are not allowed to start this exam',
                'status' => "forbidden"
            ], 401);
        }

        if ($student_finished_exam || $student_duration_passed) {
            return response()->json([
                'message' => 'You already answered this exam',
                'status' => "answered"
            ], 401);
        }

        if (!$student && $exam->visible && !$exam_ended) {
            $exam->students()->attach($request->user()->student->id, ['status' => 'started']);
        }
        // if it's the first time the student is taking the exam then send the duration in seconds
        // else send the time left in seconds
        $time_left = $student ? $student->pivot->created_at->addMinutes($exam->duration)->diffInSeconds(now()) : $exam->duration * 60;


        return ExamAndQuestionsResource::make($exam->load('questions.options'))->additional(['time_left' => $time_left]);
    }
}
