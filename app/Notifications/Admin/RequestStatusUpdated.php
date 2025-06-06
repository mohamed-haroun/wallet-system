<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public $request,
        public $processedBy
    ) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Request {$this->request->id} Status Updated")
            ->line("The request #{$this->request->id} has been {$this->request->status} by {$this->processedBy->name}.")
            ->line("Type: " . ucfirst($this->request->type))
            ->line("Amount: {$this->request->amount} EGP")
            ->lineIf(
                $this->request->status === 'rejected',
                "Reason: {$this->request->rejection_reason}"
            )
            ->action('View Request', url("/admin/requests/{$this->request->id}"));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'request_status_updated',
            'request_id' => $this->request->id,
            'request_type' => $this->request->type,
            'status' => $this->request->status,
            'amount' => $this->request->amount,
            'processed_by' => $this->processedBy->id,
            'processed_by_name' => $this->processedBy->name,
            'message' => "Request #{$this->request->id} {$this->request->status} by {$this->processedBy->name}",
        ];
    }
}
