<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewTopupRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $request) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Top-up Request')
            ->line('A user has requested a top-up.')
            ->action('View Request', url("/admin/requests/{$this->request->id}"))
            ->line("Amount: {$this->request->amount} EGP")
            ->line("User: {$this->request->user->name}");
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'topup_request',
            'request_id' => $this->request->id,
            'amount' => $this->request->amount,
            'user_id' => $this->request->user_id,
            'message' => 'New top-up request from user',
        ];
    }
}
