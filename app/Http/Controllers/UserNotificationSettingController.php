<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Http\Request;

class UserNotificationSettingController extends Controller
{
    public function index(User $user)
    {
        return response()->json($user->notificationSettings);
    }

    public function update(Request $request, User $user, $notificationType)
    {
        $validated = $request->validate([
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'push_enabled' => 'boolean',
        ]);

        $setting = UserNotificationSetting::updateOrCreate(
            [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
            ],
            $validated
        );

        return response()->json($setting);
    }
}
