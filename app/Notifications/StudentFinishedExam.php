<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentFinishedExam extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private $student, private $exam)
    {
        $this->student = $student;
        $this->exam = $exam;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Student ' . $this->student->full_name . ' finished '. $this->exam->title . '.'
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'student_id' => $this->id,
            'read_at' => null,
            'created_at' => now(),
            'data' => [
                'message' => 'Student ' . $this->student->full_name . ' finished '. $this->exam->title . '.',
            ]
        ]);
    }

    public function broadcastAs(): string
    {
        return 'student.answered';
    }
}
