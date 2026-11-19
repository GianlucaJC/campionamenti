<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('microbiological_check_points', function (Blueprint $table): void {
            foreach (['r2a', 'coliform', 'pseudomonas', 'enterococci'] as $analysis) {
                $table->decimal("{$analysis}_cfu_per_ml", 12, 2)->nullable();
                $table->date("{$analysis}_incubation_finished_on")->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('microbiological_check_points', function (Blueprint $table): void {
            foreach (['r2a', 'coliform', 'pseudomonas', 'enterococci'] as $analysis) {
                $table->dropColumn(["{$analysis}_cfu_per_ml", "{$analysis}_incubation_finished_on"]);
            }
        });
    }
};
