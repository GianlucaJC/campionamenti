<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->uuid('sampling_session_id')->nullable()->after('monitoring_section_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropIndex(['sampling_session_id']);
            $table->dropColumn('sampling_session_id');
        });
    }
};