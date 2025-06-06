<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        return response()->json(Permission::with('group')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'nullable|exists:permission_groups,id',
            'name' => 'required|string|max:255|unique:permissions',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'guard_name' => 'required|string|max:255',
        ]);

        $permission = Permission::create($validated);

        return response()->json($permission->load('group'), 201);
    }

    public function show(Permission $permission)
    {
        return response()->json($permission->load('group'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'group_id' => 'nullable|exists:permission_groups,id',
            'name' => 'sometimes|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:255',
            'guard_name' => 'sometimes|string|max:255',
        ]);

        $permission->update($validated);

        return response()->json($permission->fresh()->load('group'));
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json(null, 204);
    }
}
