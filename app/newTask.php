<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class newTask extends Mailable implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */


    public function __construct(public User $user, public Task $task)
    {
        //
    }


    public function content(): Content
    {
        return new Content(
            view: 'mail.newTask', // This is the blade view name path
        );
    }
}
