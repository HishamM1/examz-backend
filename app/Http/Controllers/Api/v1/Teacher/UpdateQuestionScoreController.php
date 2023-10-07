<?php

namespace App\Http\Controllers\Api\v1\Teacher;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateScoreExam;
use App\Models\Exam;
use Illuminate\Http\Request;

class UpdateQuestionScoreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, int $exam_id, int $student_id, int $question_id)
    {
        $exam = Exam::findOrFail($exam_id);

        // score must be between 0 and question score
        $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:' . $exam->questions()->find($question_id)->score],
        ]);

        $exam->studentAnswers()->where('student_id', $student_id)->where('question_id', $question_id)->update(['score' => $request->score]);
        
        UpdateScoreExam::dispatch($exam, $student_id)->onQueue('score-exam');
        
        return response()->json(['message' => 'Score updated successfully']);
    }
}
