<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    public function index()
    {
        return response()->json(TransactionType::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:transaction_types',
            'name' => 'required|string|max:255',
            'flow' => 'required|in:in,out',
            'requires_approval' => 'boolean',
        ]);

        $transactionType = TransactionType::create($validated);

        return response()->json($transactionType, 201);
    }

    public function show(TransactionType $transactionType)
    {
        return response()->json($transactionType);
    }

    public function update(Request $request, TransactionType $transactionType)
    {
        $validated = $request->validate([
            'code' => 'sometimes|string|max:255|unique:transaction_types,code,' . $transactionType->id,
            'name' => 'sometimes|string|max:255',
            'flow' => 'sometimes|in:in,out',
            'requires_approval' => 'sometimes|boolean',
        ]);

        $transactionType->update($validated);

        return response()->json($transactionType);
    }

    public function destroy(TransactionType $transactionType)
    {
        $transactionType->delete();

        return response()->json(null, 204);
    }
}
