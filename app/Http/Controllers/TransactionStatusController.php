<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionStatusRequest;
use App\Http\Requests\UpdateTransactionStatusRequest;
use App\Models\TransactionStatus;

class TransactionStatusController extends Controller
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
    public function store(StoreTransactionStatusRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionStatus $transactionStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionStatus $transactionStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionStatusRequest $request, TransactionStatus $transactionStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionStatus $transactionStatus)
    {
        //
    }
}
