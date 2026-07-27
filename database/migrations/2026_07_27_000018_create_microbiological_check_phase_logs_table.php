<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('microbiological_check_phase_logs')) {
            Schema::table('microbiological_check_phase_logs', function (Blueprint $table): void {
                $table->index(['microbiological_check_id', 'logged_at'], 'check_phase_logs_check_time_idx');
            });

            return;
        }

        Schema::create('microbiological_check_phase_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('microbiological_check_id');
            $table->string('phase', 30);
            $table->string('action', 30);
            $table->unsignedBigInteger('performed_by_user_id')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->foreign('microbiological_check_id', 'check_phase_logs_check_fk')
                ->references('id')->on('microbiological_checks')->cascadeOnDelete();
            $table->foreign('performed_by_user_id', 'check_phase_logs_user_fk')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['microbiological_check_id', 'logged_at'], 'check_phase_logs_check_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('microbiological_check_phase_logs');
    }
};