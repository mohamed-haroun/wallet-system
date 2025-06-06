<?php

namespace App\Http\Controllers;

use App\Models\AdminProfile;
use App\Models\User;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    public function index()
    {
        return response()->json(AdminProfile::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:users,id',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $adminProfile = AdminProfile::create($validated);

        return response()->json($adminProfile, 201);
    }

    public function show(AdminProfile $adminProfile)
    {
        return response()->json($adminProfile);
    }

    public function update(Request $request, AdminProfile $adminProfile)
    {
        $validated = $request->validate([
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $adminProfile->update($validated);

        return response()->json($adminProfile);
    }

    public function destroy(AdminProfile $adminProfile)
    {
        $adminProfile->delete();

        return response()->json(null, 204);
    }
}
