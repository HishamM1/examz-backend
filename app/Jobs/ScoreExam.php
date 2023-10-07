<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Exam;
use App\Models\StudentAnswer;
use App\Notifications\ExamScored;

class ScoreExam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * Create a new job instance.
     */
    public function __construct(private $exam, private $student_id)
    {
        $this->exam = $exam->withoutRelations();
        $this->student_id = $student_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $student_answers = $this->exam->studentAnswers()->where('student_id', $this->student_id)->get();
        $correct_answers = $this->exam->teacherAnswers()->with('question')->get();

        $score = 0;

        foreach ($student_answers as $student_answer) {
            $correct_answer = $correct_answers->where('question_id', $student_answer->question_id)->first();


            if (($student_answer->answer == $correct_answer->answer) || ($student_answer->similarity >= 0.6)) {
                // update score for student answer
                StudentAnswer::where(['student_id' => $this->student_id, "question_id" => $student_answer->question_id])->update(['score' => $correct_answer->question->score]);

                $score += $correct_answer->question->score;
            }
        }

        $this->exam->students()->updateExistingPivot($this->student_id, ['total_score' => $score]);

        // send notification to student
        $this->exam->students()->where('student_id', $this->student_id)->first()->user->notify(new ExamScored($this->exam));
    }
}
