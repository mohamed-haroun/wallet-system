<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TopupRequestProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $request) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Top-up Request ' . ucfirst($this->request->status))
            ->line("Your top-up request has been {$this->request->status}.")
            ->line("Amount: {$this->request->amount} EGP")
            ->lineIf(
                $this->request->status === 'rejected',
                "Reason: {$this->request->rejection_reason}"
            );
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'topup_request_processed',
            'status' => $this->request->status,
            'amount' => $this->request->amount,
            'message' => "Your top-up request has been {$this->request->status}",
        ];
    }
}
