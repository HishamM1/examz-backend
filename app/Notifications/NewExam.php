<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewExam extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private $exam, private $teacher_name)
    {
        $this->exam = $exam;
        $this->teacher_name = $teacher_name;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Exam')
            ->line('Your teacher( ' . $this->teacher_name . ' ) has created a new exam.')
            ->line('Exam title: ' . $this->exam->title)
            ->line('Exam description: ' . $this->exam->description)
            ->line('Exam start time: ' . $this->exam->start_time)
            ->line('Exam duration: ' . $this->exam->duration)
            ->line('Exam subject: ' . $this->exam->subject)
            ->action('Go to exam', url('/api/v1/student/exams/' . $this->exam->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your teacher ( ' . $this->teacher_name . ' ) has created a new exam.',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'exam_id' => $this->exam->id,
            'read_at' => null,
            'created_at' => now(),
            'data' => [
                'message' => 'Your teacher ( ' . $this->teacher_name . ' ) has created a new exam.',
            ],
        ]);
    }

    public function broadcastAs(): string
    {
        return 'new.exam';
    }
}
