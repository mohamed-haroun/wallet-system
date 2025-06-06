<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        return response()->json(NotificationTemplate::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|json',
            'type' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $template = NotificationTemplate::create($validated);

        return response()->json($template, 201);
    }

    public function show(NotificationTemplate $notificationTemplate)
    {
        return response()->json($notificationTemplate);
    }

    public function update(Request $request, NotificationTemplate $notificationTemplate)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'variables' => 'nullable|json',
            'type' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $notificationTemplate->update($validated);

        return response()->json($notificationTemplate);
    }

    public function destroy(NotificationTemplate $notificationTemplate)
    {
        $notificationTemplate->delete();

        return response()->json(null, 204);
    }
}
