<?php

namespace App\Jobs;

use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;


class CheckExamDuration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
                // check if the exam is started and the created at time passed the duration of the exam, then change the status to finished
                $exams = DB::table('student_exam')->where('status', 'started')->get();

                foreach ($exams as $exam) {
                    $exam_duration = Exam::select('id','duration')->find($exam->exam_id)->duration;
                    $exam_start_time = Carbon::parse($exam->created_at);
                    $exam_end_time = $exam_start_time->addMinutes((int) $exam_duration);
        
                    if ($exam_end_time <= now()) {
                        DB::table('student_exam')->where('exam_id', $exam->exam_id)->update(['status' => 'finished']);
                    }
                }
    }
}


