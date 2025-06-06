<?php

namespace App\Http\Controllers;

use App\Models\ReferralProgram;
use Illuminate\Http\Request;

class ReferralProgramController extends Controller
{
    public function index()
    {
        return response()->json(ReferralProgram::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:referral_programs',
            'referrer_reward' => 'required|numeric',
            'referee_reward' => 'required|numeric',
            'reward_type' => 'required|in:fixed,percentage',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $program = ReferralProgram::create($validated);

        return response()->json($program, 201);
    }

    public function show(ReferralProgram $referralProgram)
    {
        return response()->json($referralProgram);
    }

    public function update(Request $request, ReferralProgram $referralProgram)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:255|unique:referral_programs,code,' . $referralProgram->id,
            'referrer_reward' => 'sometimes|numeric',
            'referee_reward' => 'sometimes|numeric',
            'reward_type' => 'sometimes|in:fixed,percentage',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        $referralProgram->update($validated);

        return response()->json($referralProgram);
    }

    public function destroy(ReferralProgram $referralProgram)
    {
        $referralProgram->delete();

        return response()->json(null, 204);
    }
}
