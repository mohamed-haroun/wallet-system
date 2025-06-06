<?php

namespace App\Http\Controllers;

use App\Models\RequestType;
use Illuminate\Http\Request;

class RequestTypeController extends Controller
{
    public function index()
    {
        return response()->json(RequestType::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:request_types',
        ]);

        $requestType = RequestType::create($validated);

        return response()->json($requestType, 201);
    }

    public function show(RequestType $requestType)
    {
        return response()->json($requestType);
    }

    public function update(Request $request, RequestType $requestType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:255|unique:request_types,code,' . $requestType->id,
        ]);

        $requestType->update($validated);

        return response()->json($requestType);
    }

    public function destroy(RequestType $requestType)
    {
        $requestType->delete();

        return response()->json(null, 204);
    }
}
