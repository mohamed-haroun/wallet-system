<?php

namespace App\Http\Controllers;

use App\Models\WalletType;
use Illuminate\Http\Request;

class WalletTypeController extends Controller
{
    public function index()
    {
        return response()->json(WalletType::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'currency_code' => 'required|string|size:3',
            'allow_negative' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $walletType = WalletType::create($validated);

        return response()->json($walletType, 201);
    }

    public function show(WalletType $walletType)
    {
        return response()->json($walletType);
    }

    public function update(Request $request, WalletType $walletType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'currency_code' => 'sometimes|string|size:3',
            'allow_negative' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $walletType->update($validated);

        return response()->json($walletType);
    }

    public function destroy(WalletType $walletType)
    {
        $walletType->delete();

        return response()->json(null, 204);
    }
}
