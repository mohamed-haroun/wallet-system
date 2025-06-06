<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminRequestController extends Controller
{
    // AdminRequestController.php
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string',
            'request_type' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $requests = Request::with(['user', 'requestType', 'status'])
            ->filter($validator->validated())
            ->paginate(15);

        return response()->json($requests);
    }
}
