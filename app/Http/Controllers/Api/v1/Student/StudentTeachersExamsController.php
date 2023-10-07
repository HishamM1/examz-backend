<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentTeachersExamsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $student_id = $request->user()->student->id;

        $teachers = DB::table('teacher_student')
            ->where('student_id', $student_id)
            ->pluck('teacher_id');

        $in_progress_exam = DB::table('student_exam')
            ->where('student_id', $student_id)
            ->where('status', 'started')
            ->pluck('exam_id');

        $finished_exams = DB::table('student_exam')
            ->where('student_id', $student_id)
            ->where('status', 'finished')
            ->get();

        $taken_exams = $finished_exams->filter(function ($exam) {
            return $exam->total_score !== null;
        })->pluck('exam_id');

        $calculating_total_score = $finished_exams->filter(function ($exam) {
            return $exam->total_score === null;
        })->pluck('exam_id');
            

        $exams = Exam::whereIntegerInRaw('teacher_id', $teachers)
            ->with(['teacher.user:id,full_name,email,profile_picture,about'])
            ->filter($request->only(['search', 'active', 'sortBy', 'status']))
            ->fastPaginate(8)
            ->withQueryString();

        // if the exam id exists in the taken exams and the taken exam status is finished, then add the taken property to the exam
        $exams->getCollection()->transform(function ($exam) use ($taken_exams, $in_progress_exam, $calculating_total_score) {
            if ($taken_exams->contains($exam->id)) {
                $exam->status = "taken";
            } elseif ($in_progress_exam->contains($exam->id)) {
                $exam->status = 'in_progress';
            } elseif ($calculating_total_score->contains($exam->id)) {
                $exam->status = 'calculating_total_score';
            } else {
                $exam->status = 'not_taken';
            }
            return $exam;
        });

        return ExamResource::collection($exams);
    }
}
