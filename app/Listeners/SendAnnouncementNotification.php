<?php

namespace App\Listeners;

use App\Events\AnnouncementCreated;
use App\Notifications\NewAnnouncement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAnnouncementNotification implements ShouldQueue
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
    public function handle(AnnouncementCreated $event): void
    {
        $event->students->each(function ($student) use($event) {
            $student->notify(new NewAnnouncement($event->teacher_name));
        });
    }
}
