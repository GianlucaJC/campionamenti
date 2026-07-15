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
        Schema::table('monitoring_sections', function (Blueprint $table): void {
            $table->string('sub_environment', 50)->nullable()->after('environment');
            $table->index(['environment', 'sub_environment'], 'monitoring_sections_env_sub_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_sections', function (Blueprint $table): void {
            $table->dropIndex('monitoring_sections_env_sub_idx');
            $table->dropColumn('sub_environment');
        });
    }
};
