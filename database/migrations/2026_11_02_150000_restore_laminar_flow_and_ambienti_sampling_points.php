<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $departmentIds = DB::table('monitoring_departments')
            ->whereRaw('LOWER(name) = ?', ['laminar flow e ambienti'])
            ->pluck('id');

        if ($departmentIds->isEmpty()) {
            return;
        }

        DB::table('monitoring_departments')
            ->whereIn('id', $departmentIds)
            ->update([
                'deleted_at' => null,
                'updated_at' => now(),
            ]);

        DB::table('sampling_points')
            ->whereIn('monitoring_department_id', $departmentIds)
            ->update(['is_active' => true]);
    }

    public function down(): void
    {
    }
};
