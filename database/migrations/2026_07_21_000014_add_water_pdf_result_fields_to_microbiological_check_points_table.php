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
        Schema::table('microbiological_check_points', function (Blueprint $table): void {
            $table->unsignedInteger('aerobic_plate_cfu')->nullable()->after('cfu_count');
            $table->unsignedInteger('aerobic_cfu_per_ml')->nullable()->after('aerobic_plate_cfu');
            $table->unsignedInteger('coliform_plate_cfu')->nullable()->after('coliform_result');
            $table->unsignedInteger('coliform_confirmed_cfu')->nullable()->after('coliform_plate_cfu');
            $table->unsignedInteger('coliform_cfu_per_100ml')->nullable()->after('coliform_confirmed_cfu');
            $table->unsignedInteger('pseudomonas_plate_cfu')->nullable()->after('pseudomonas_result');
            $table->unsignedInteger('pseudomonas_confirmed_cfu')->nullable()->after('pseudomonas_plate_cfu');
            $table->unsignedInteger('pseudomonas_cfu_per_100ml')->nullable()->after('pseudomonas_confirmed_cfu');
            $table->unsignedInteger('enterococci_plate_cfu')->nullable()->after('enterococci_result');
            $table->unsignedInteger('enterococci_confirmed_cfu')->nullable()->after('enterococci_plate_cfu');
            $table->unsignedInteger('enterococci_cfu_per_100ml')->nullable()->after('enterococci_confirmed_cfu');
            $table->string('appearance_result', 20)->nullable()->after('ph_value');
            $table->string('final_result', 20)->nullable()->after('appearance_result');
        });

        DB::table('microbiological_check_points')
            ->whereNotNull('cfu_count')
            ->update(['aerobic_cfu_per_ml' => DB::raw('cfu_count')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microbiological_check_points', function (Blueprint $table): void {
            $table->dropColumn([
                'aerobic_plate_cfu',
                'aerobic_cfu_per_ml',
                'coliform_plate_cfu',
                'coliform_confirmed_cfu',
                'coliform_cfu_per_100ml',
                'pseudomonas_plate_cfu',
                'pseudomonas_confirmed_cfu',
                'pseudomonas_cfu_per_100ml',
                'enterococci_plate_cfu',
                'enterococci_confirmed_cfu',
                'enterococci_cfu_per_100ml',
                'appearance_result',
                'final_result',
            ]);
        });
    }
};
