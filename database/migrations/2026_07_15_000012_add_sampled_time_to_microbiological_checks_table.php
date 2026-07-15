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
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->time('sampled_time')->nullable()->after('sampled_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropColumn('sampled_time');
        });
    }
};