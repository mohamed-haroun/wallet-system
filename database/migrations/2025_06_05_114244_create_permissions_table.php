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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('name'); // e.g., 'withdrawal.approve'
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('guard_name')->default('admin');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('permission_groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
