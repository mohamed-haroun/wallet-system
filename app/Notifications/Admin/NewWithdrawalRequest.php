<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewWithdrawalRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $request) {}

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Withdrawal Request')
            ->line('A new withdrawal request has been created.')
            ->action('View Request', url("/admin/requests/{$this->request->id}"))
            ->line("Amount: {$this->request->amount} EGP");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'withdrawal_request',
            'request_id' => $this->request->id,
            'amount' => $this->request->amount,
            'message' => 'New withdrawal request created',
        ];
    }

    // public function toBroadcast($notifiable)
    // {
    //     return new BroadcastMessage($this->toArray($notifiable));
    // }
}
