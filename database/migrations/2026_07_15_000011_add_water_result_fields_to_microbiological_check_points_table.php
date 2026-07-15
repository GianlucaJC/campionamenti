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
            $table->string('coliform_result', 20)->nullable()->after('second_growth_result');
            $table->string('pseudomonas_result', 20)->nullable()->after('coliform_result');
            $table->string('enterococci_result', 20)->nullable()->after('pseudomonas_result');
            $table->string('ph_value', 20)->nullable()->after('enterococci_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microbiological_check_points', function (Blueprint $table): void {
            $table->dropColumn([
                'coliform_result',
                'pseudomonas_result',
                'enterococci_result',
                'ph_value',
            ]);
        });
    }
};