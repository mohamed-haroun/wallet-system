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
        Schema::create('wallet_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->decimal('balance_before', 20, 4);
            $table->decimal('balance_after', 20, 4);
            $table->decimal('amount', 20, 4);
            $table->string('reference');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_ledgers');
    }
};
