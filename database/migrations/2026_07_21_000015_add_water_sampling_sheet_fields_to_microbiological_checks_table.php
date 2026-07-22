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
            $table->string('facility_name')->nullable()->after('monitoring_section_id');
            $table->date('r2a_agar_expires_on')->nullable()->after('r2a_agar_lot');
            $table->string('r2a_incubator_code')->nullable()->after('r2a_agar_expires_on');
            $table->date('r2a_incubation_started_on')->nullable()->after('r2a_incubator_code');
            $table->date('r2a_incubation_finished_on')->nullable()->after('r2a_incubation_started_on');
            $table->date('coliform_agar_expires_on')->nullable()->after('coliform_agar_lot');
            $table->string('coliform_incubator_code')->nullable()->after('coliform_agar_expires_on');
            $table->date('coliform_incubation_started_on')->nullable()->after('coliform_incubator_code');
            $table->date('coliform_incubation_finished_on')->nullable()->after('coliform_incubation_started_on');
            $table->date('pseudomonas_cn_expires_on')->nullable()->after('pseudomonas_cn_lot');
            $table->string('pseudomonas_incubator_code')->nullable()->after('pseudomonas_cn_expires_on');
            $table->date('pseudomonas_incubation_started_on')->nullable()->after('pseudomonas_incubator_code');
            $table->date('pseudomonas_incubation_finished_on')->nullable()->after('pseudomonas_incubation_started_on');
            $table->date('slanetz_bartley_expires_on')->nullable()->after('slanetz_bartley_lot');
            $table->string('enterococci_incubator_code')->nullable()->after('slanetz_bartley_expires_on');
            $table->date('enterococci_incubation_started_on')->nullable()->after('enterococci_incubator_code');
            $table->date('enterococci_incubation_finished_on')->nullable()->after('enterococci_incubation_started_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropColumn([
                'facility_name',
                'r2a_agar_expires_on',
                'r2a_incubator_code',
                'r2a_incubation_started_on',
                'r2a_incubation_finished_on',
                'coliform_agar_expires_on',
                'coliform_incubator_code',
                'coliform_incubation_started_on',
                'coliform_incubation_finished_on',
                'pseudomonas_cn_expires_on',
                'pseudomonas_incubator_code',
                'pseudomonas_incubation_started_on',
                'pseudomonas_incubation_finished_on',
                'slanetz_bartley_expires_on',
                'enterococci_incubator_code',
                'enterococci_incubation_started_on',
                'enterococci_incubation_finished_on',
            ]);
        });
    }
};
