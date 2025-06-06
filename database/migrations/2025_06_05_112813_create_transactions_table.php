<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->unsignedBigInteger('transaction_type_id');
            $table->unsignedBigInteger('status_id');
            $table->decimal('amount', 20, 4);
            $table->decimal('fee', 20, 4)->default(0);
            $table->decimal('net_amount', 20, 4);
            $table->string('reference')->unique();
            $table->string('external_reference')->nullable();
            $table->text('narration')->nullable();
            $table->json('meta')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('device_id')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->uuid('reversed_by')->nullable();
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types');
            $table->foreign('status_id')->references('id')->on('transaction_statuses');
            $table->index('reference');
            $table->index('external_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
