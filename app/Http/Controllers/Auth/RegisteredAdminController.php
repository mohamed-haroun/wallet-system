<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminProfile;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredAdminController extends Controller
{
    /**
     * Show the admin registration form.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Auth/Register');
    }

    /**
     * Handle an incoming admin registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type_id' => UserType::where('name', 'admin')->first()->id,
            'is_active' => true,
        ]);

        AdminProfile::create([
            'admin_id' => $user->id,
            'position' => $request->position,
            'department' => $request->department,
        ]);

        event(new Registered($user));

        Auth::guard('admin')->login($user);

        return to_route('admin.dashboard');
    }
}
