<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('microbiological_check_phase_logs', function (Blueprint $table): void {
            $table->text('reason')->nullable()->after('action');
        });
    }

    public function down(): void
    {
        Schema::table('microbiological_check_phase_logs', function (Blueprint $table): void {
            $table->dropColumn('reason');
        });
    }
};