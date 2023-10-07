<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAnnouncement extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private string $teacher_name)
    {
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
                            ->subject('New Announcement')
                            ->line("Your teacher(".$this->teacher_name.") has posted a new announcement.")
                            ->action('Go to announcements', url('/api/v1/student/announcements'));
                    
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your teacher (". $this->teacher_name. ") has posted a new announcement.",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'read_at' => null,
            'created_at' => now(),
            'data' => [
                'message' => "Your teacher (". $this->teacher_name .") has posted a new announcement.",
            ],
        ]);
    }

    public function broadcastAs(): string
    {
        return 'new.announcement';
    }

}
