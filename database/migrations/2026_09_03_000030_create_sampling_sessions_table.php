<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sampling_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('environment', 50);
            $table->string('sub_environment', 50)->nullable();
            $table->json('header');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sampling_sessions');
    }
};