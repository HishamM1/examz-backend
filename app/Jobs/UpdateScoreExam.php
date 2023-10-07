<?php

namespace App\Jobs;

use App\Models\Exam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateScoreExam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $exam;

    private $student_id;
    /**
     * Create a new job instance.
     */
    public function __construct(Exam $exam, int $student_id)
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

        $score = 0;

        foreach ($student_answers as $student_answer) {
            $score += $student_answer->score;
        }

        $this->exam->students()->updateExistingPivot($this->student_id, ['total_score' => $score]);
    }
}
