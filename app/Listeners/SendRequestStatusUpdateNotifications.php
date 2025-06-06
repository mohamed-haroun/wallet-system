<?php

namespace App\Listeners;

use App\Events\RequestStatusUpdated;
use App\Models\User;
use App\Models\UserType;
use App\Notifications\Admin\RequestStatusUpdated as RequestStatusUpdatedNotification;

class SendRequestStatusUpdateNotifications
{
    public function handle(RequestStatusUpdated $event)
    {
        // Notify the requester
        if ($event->request->user) {
            $event->request->user->notify(
                new \App\Notifications\Admin\RequestStatusUpdated($event->request, $event->processedBy)
            );
        }

        // Notify all admins about the status change
        $admins = User::where('user_type_id', UserType::where('name', 'admin')->first()->id)
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new RequestStatusUpdatedNotification($event->request, $event->processedBy)
            );
        }
    }
}
