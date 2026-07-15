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
            $table->string('membrane_lot')->nullable()->after('swab_lot');
            $table->string('bottle_sterilization_lot')->nullable()->after('membrane_lot');
            $table->string('r2a_agar_lot')->nullable()->after('bottle_sterilization_lot');
            $table->string('coliform_agar_lot')->nullable()->after('r2a_agar_lot');
            $table->string('pseudomonas_cn_lot')->nullable()->after('coliform_agar_lot');
            $table->string('slanetz_bartley_lot')->nullable()->after('pseudomonas_cn_lot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropColumn([
                'membrane_lot',
                'bottle_sterilization_lot',
                'r2a_agar_lot',
                'coliform_agar_lot',
                'pseudomonas_cn_lot',
                'slanetz_bartley_lot',
            ]);
        });
    }
};