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
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('program_id');
            $table->uuid('referrer_id');
            $table->uuid('referee_id');
            $table->decimal('referrer_reward', 20, 4);
            $table->decimal('referee_reward', 20, 4);
            $table->uuid('referrer_transaction_id')->nullable();
            $table->uuid('referee_transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed']);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('referral_programs');
            $table->foreign('referrer_id')->references('id')->on('users');
            $table->foreign('referee_id')->references('id')->on('users');
            $table->foreign('referrer_transaction_id')->references('id')->on('transactions');
            $table->foreign('referee_transaction_id')->references('id')->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
