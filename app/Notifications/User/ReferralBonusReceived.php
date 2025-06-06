<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralBonusReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public $referral,
        public $bonusAmount
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Referral Bonus Received!')
            ->line("You've received a referral bonus of {$this->bonusAmount} EGP!")
            ->line("New user joined using your referral code: {$this->referral->referee->email}")
            ->line("Your current balance: {$notifiable->wallet->balance} EGP")
            ->action('View Wallet', url('/wallet'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'referral_bonus',
            'referral_id' => $this->referral->id,
            'bonus_amount' => $this->bonusAmount,
            'referee_email' => $this->referral->referee->email,
            'message' => "You received {$this->bonusAmount} EGP referral bonus",
        ];
    }
}
