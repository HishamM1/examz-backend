<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamScored extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private $exam)
    {
        $this->exam = $exam->withoutRelations();
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
            ->line("Your exam {$this->exam->title} has been scored.")
            ->action('View Exam', url('/api/v1/student/exams/' . $this->exam->id . '/result'))
            ->line('Thank you for using Examz!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your exam {$this->exam->title} has been scored.",
            'link' => '/student/exams/' . $this->exam->id . '/result',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'data' => [
                'message' => "Your exam {$this->exam->title} has been scored.",
                'link' => '/student/exams/' . $this->exam->id . '/result',
            ],
            'read_at' => null,
            'created_at' => now(),
        ]);
    }

    public function broadcastAs(): string
    {
        return 'exam.scored';
    }


}
