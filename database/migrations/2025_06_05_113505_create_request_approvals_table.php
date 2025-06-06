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
        Schema::create('request_approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('request_id');
            $table->uuid('admin_id');
            $table->enum('action', ['approve', 'reject', 'hold']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('requests');
            $table->foreign('admin_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_approvals');
    }
};
