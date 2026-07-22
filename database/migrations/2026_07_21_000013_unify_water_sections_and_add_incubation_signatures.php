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
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->string('incubation_started_signature')->nullable()->after('operator_name');
            $table->string('incubation_finished_signature')->nullable()->after('incubation_started_signature');
        });

        $legacySections = DB::table('monitoring_sections')
            ->whereIn('code', ['acque_sede_1', 'acque_sede_2'])
            ->get(['id']);

        if ($legacySections->isEmpty()) {
            return;
        }

        $now = now();
        $waterSectionId = DB::table('monitoring_sections')->where('code', 'acque')->value('id');

        if (! $waterSectionId) {
            $waterSectionId = DB::table('monitoring_sections')->insertGetId([
                'code' => 'acque',
                'environment' => 'acque',
                'sub_environment' => null,
                'name' => 'Acque',
                'description' => 'Campionamento microbiologico dell\'acqua.',
                'sort_order' => 120,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $legacySectionIds = $legacySections->pluck('id')->all();
        $departmentId = DB::table('monitoring_departments')
            ->where('monitoring_section_id', $waterSectionId)
            ->where('name', 'Punti uso')
            ->value('id');

        if (! $departmentId) {
            $departmentId = DB::table('monitoring_departments')->insertGetId([
                'monitoring_section_id' => $waterSectionId,
                'code' => 'punti_uso',
                'name' => 'Punti uso',
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('microbiological_checks')
            ->whereIn('monitoring_section_id', $legacySectionIds)
            ->update(['monitoring_section_id' => $waterSectionId, 'updated_at' => $now]);

        DB::table('sampling_points')
            ->whereIn('monitoring_section_id', $legacySectionIds)
            ->update([
                'monitoring_section_id' => $waterSectionId,
                'monitoring_department_id' => $departmentId,
                'updated_at' => $now,
            ]);

        DB::table('monitoring_departments')
            ->whereIn('monitoring_section_id', $legacySectionIds)
            ->delete();

        DB::table('monitoring_sections')
            ->whereIn('id', $legacySectionIds)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropColumn([
                'incubation_started_signature',
                'incubation_finished_signature',
            ]);
        });
    }
};
