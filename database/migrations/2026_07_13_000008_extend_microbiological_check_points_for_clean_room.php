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
        Schema::table('microbiological_check_points', function (Blueprint $table): void {
            $table->time('exposure_ended_at')->nullable()->after('sampled_at');
            $table->unsignedInteger('first_cfu_count')->nullable()->after('cfu_count');
            $table->unsignedInteger('second_cfu_count')->nullable()->after('first_cfu_count');
            $table->string('first_growth_result', 20)->nullable()->after('second_cfu_count');
            $table->string('second_growth_result', 20)->nullable()->after('first_growth_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microbiological_check_points', function (Blueprint $table): void {
            $table->dropColumn([
                'exposure_ended_at',
                'first_cfu_count',
                'second_cfu_count',
                'first_growth_result',
                'second_growth_result',
            ]);
        });
    }
};
