<?php

namespace App\Listeners;

use App\Events\ExamCreated;
use App\Notifications\NewExam;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExamNotification implements ShouldQueue
{
    public $queue = 'notifications';
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExamCreated $event): void
    {
        $event->students->each(function ($student) use($event) {
            $student->notify(new NewExam($event->exam, $event->teacher_name));
        });
        
    }
}
