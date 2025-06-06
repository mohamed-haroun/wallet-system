<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserNotificationSettingRequest;
use App\Http\Requests\UpdateUserNotificationSettingRequest;
use App\Models\UserNotificationSetting;

class UserNotificationSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserNotificationSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserNotificationSetting $userNotificationSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserNotificationSetting $userNotificationSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserNotificationSettingRequest $request, UserNotificationSetting $userNotificationSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserNotificationSetting $userNotificationSetting)
    {
        //
    }
}
