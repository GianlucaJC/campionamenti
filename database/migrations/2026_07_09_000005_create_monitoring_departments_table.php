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
        Schema::create('monitoring_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_section_id')->constrained()->cascadeOnDelete();
            $table->string('code', 50)->nullable();
            $table->string('name');
            $table->decimal('sort_order', 8, 3)->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['monitoring_section_id', 'name']);
        });

        Schema::table('sampling_points', function (Blueprint $table) {
            $table->foreignId('monitoring_department_id')
                ->nullable()
                ->after('monitoring_section_id')
                ->constrained('monitoring_departments')
                ->nullOnDelete();
        });

        $this->backfillDepartments();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sampling_points', function (Blueprint $table) {
            $table->dropConstrainedForeignId('monitoring_department_id');
        });

        Schema::dropIfExists('monitoring_departments');
    }

    private function backfillDepartments(): void
    {
        $sections = DB::table('monitoring_sections')->select('id')->get();

        foreach ($sections as $section) {
            $pointRows = DB::table('sampling_points')
                ->where('monitoring_section_id', $section->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'title']);

            $departmentIds = [];
            $nextSortOrder = 10;

            foreach ($pointRows as $point) {
                $departmentName = $this->extractDepartmentName((string) $point->title);

                if (! isset($departmentIds[$departmentName])) {
                    $existing = DB::table('monitoring_departments')
                        ->where('monitoring_section_id', $section->id)
                        ->where('name', $departmentName)
                        ->first(['id']);

                    if ($existing) {
                        $departmentIds[$departmentName] = (int) $existing->id;
                    } else {
                        $departmentIds[$departmentName] = DB::table('monitoring_departments')->insertGetId([
                            'monitoring_section_id' => $section->id,
                            'name' => $departmentName,
                            'sort_order' => $nextSortOrder,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $nextSortOrder += 10;
                    }
                }

                DB::table('sampling_points')
                    ->where('id', $point->id)
                    ->update(['monitoring_department_id' => $departmentIds[$departmentName]]);
            }
        }
    }

    private function extractDepartmentName(string $title): string
    {
        $title = trim($title);

        if ($title === '') {
            return 'Senza reparto';
        }

        $separatorPosition = strpos($title, ':');

        if ($separatorPosition === false) {
            return 'Senza reparto';
        }

        $department = trim(substr($title, 0, $separatorPosition));

        return $department !== '' ? $department : 'Senza reparto';
    }
};
