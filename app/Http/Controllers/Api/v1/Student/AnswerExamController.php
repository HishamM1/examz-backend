<?php

namespace App\Http\Controllers\Api\v1\Student;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Jobs\ScoreExam;
use App\Jobs\Similarity;
use App\Notifications\StudentFinishedExam;
use Illuminate\Support\Facades\Notification;

class AnswerExamController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Exam $exam)
    {
        $request['answers'] = json_decode($request->answers, true);

        $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'exists:questions,id'],
            'answers.*.answer' => ['nullable'],
        ]);

        $answers = $request->answers;

        // Get the question IDs from the answers array
        $answeredQuestionIds = collect($answers)->pluck('question_id')->unique();
        // Get the question IDs from the exam
        $examQuestionIds = $exam->questions()->pluck('id');
        // Check if the answered question IDs are the same as the exam question IDs
        if (!$examQuestionIds->diff($answeredQuestionIds)->isEmpty()) {
            return response()->json([
                'message' => 'All exam questions must be answered.'
            ], 422);
        }
        $student = $request->user()->student;
        // save the answers in the database
        $student_id = $student->id;
        foreach ($answers as $answer) {
            $exam->studentAnswers()->create([
                'student_id' => $student_id,
                'question_id' => $answer['question_id'],
                'answer' => $answer['answer'],
            ]);
            $question = Question::where('id',(int) $answer['question_id'])->first();

            if($question->type == 'open_ended') {
                Similarity::dispatch($exam, $student_id, $answer['question_id'],  $answer['answer'])->onQueue('question-similarity');
            }
        }

        // update the status of the exam to finished
        $student->exams()->where('exam_id', $exam->id)->update(['status' => 'finished']);

        // notify the teacher that the student finished the exam
        $teacher = $exam->teacher->user;
        Notification::send($teacher, new StudentFinishedExam($student->user, $exam));

        // score of the exam will be disbatched in the background
        ScoreExam::dispatch($exam, $student_id)->onQueue('score-exam');

        return response()->json(['message' => 'Exam answered successfully'], 201);
    }
}
