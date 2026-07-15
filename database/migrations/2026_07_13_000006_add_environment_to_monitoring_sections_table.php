<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monitoring_sections', function (Blueprint $table): void {
            $table->string('environment', 50)->default('produzione')->after('code');
            $table->index('environment');
        });

        DB::table('monitoring_sections')
            ->whereNull('environment')
            ->update(['environment' => 'produzione']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_sections', function (Blueprint $table): void {
            $table->dropIndex(['environment']);
            $table->dropColumn('environment');
        });
    }
};
