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
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('request_type_id');
            $table->uuid('user_id');
            $table->uuid('wallet_id');
            $table->decimal('amount', 20, 4);
            $table->text('details')->nullable();
            $table->unsignedBigInteger('status_id');
            $table->uuid('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->foreign('request_type_id')->references('id')->on('request_types');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->foreign('status_id')->references('id')->on('transaction_statuses');
            $table->foreign('processed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
