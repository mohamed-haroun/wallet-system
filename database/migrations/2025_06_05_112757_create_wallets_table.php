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
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->unsignedBigInteger('wallet_type_id');
            $table->decimal('available_balance', 20, 4)->default(0);
            $table->decimal('pending_balance', 20, 4)->default(0);
            $table->decimal('total_earned', 20, 4)->default(0);
            $table->decimal('total_spent', 20, 4)->default(0);
            $table->string('wallet_tag')->unique(); // WALLET-XXXX-XXXX
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('wallet_type_id')->references('id')->on('wallet_types');
            $table->index('wallet_tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
