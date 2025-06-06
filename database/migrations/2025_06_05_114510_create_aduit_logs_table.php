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
        Schema::create('aduit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event'); // created, updated, deleted, etc.
            $table->string('auditable_type');
            $table->uuid('auditable_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aduit_logs');
    }
};
