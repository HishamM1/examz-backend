<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamStudentsResource;
use App\Http\Resources\ResultResource;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Support\Facades\Gate;

class ExamStudentsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request, Exam $exam)
    {
        return ExamStudentsResource::collection($exam->students()->with('user')->when($request->input('status') == 'started', function ($query) {
                return $query->where('status','started');
            })->when($request->input('status') == 'completed', function ($query) {
                return $query->where('status','completed');
            })->get());
    }

    public function show(Request $request, $exam_id, $student_id = null)
    {
        if (!$student_id) {
            $student_id = $request->user()->student->id;
        }
        if (Gate::denies('view-results', $exam_id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $exam = Exam::findOrFail($exam_id);
        $exam_answers = $exam->teacherAnswers()->with('question.options')->get();
        $student_answers = $exam->studentAnswers()->where('student_id', $student_id)->get();
        $result = [];

        foreach ($exam_answers as $exam_answer) {
            $student_answer = $student_answers->where('question_id', $exam_answer->question_id)->first();
            // [ { question: '...' student_answer: '...', correct_answer: '...', score: '...', similarity: '...' (open-ended) } ]
            $result[] = [
                'question' => $exam_answer->question,
                'type' => $exam_answer->question->type,
                'correct_answer' => $exam_answer->answer,
                'student_answer' => $student_answer?->answer ,
                'score' => $exam_answer->question->score,
                'student_score' => $student_answer?->score,
                'is_correct' => $exam_answer->answer == $student_answer?->answer,
                'similarity' => $exam_answer->question->type == 'open_ended' ? $student_answer?->similarity : null,
            ];
        }

        return ResultResource::collection($result)->additional([
            'total_score' => $exam_answers->sum('question.score'),
            'student_score' => $student_answers?->sum("score"),
            'student_id' => $student_id,
        ]);
    }

    public function destroy($exam_id, $student_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $exam->students()->detach($student_id);

        // Delete student answers
        $exam->studentAnswers()->where('student_id', $student_id)->delete();

        return response()->json(['message' => 'Student deleted successfully'], 200);
    }
}
