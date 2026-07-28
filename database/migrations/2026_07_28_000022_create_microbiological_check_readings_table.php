<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('microbiological_check_readings');

        Schema::create('microbiological_check_readings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('microbiological_check_point_id');
            $table->unsignedTinyInteger('reading_number');
            $table->unsignedInteger('cfu_count')->nullable();
            $table->string('growth_result', 20)->nullable();
            $table->timestamps();

            $table->foreign('microbiological_check_point_id', 'check_readings_point_fk')
                ->references('id')->on('microbiological_check_points')->cascadeOnDelete();
            $table->unique(['microbiological_check_point_id', 'reading_number'], 'check_point_reading_unique');
        });

        DB::table('microbiological_check_points')
            ->where(function ($query): void {
                $query->whereNotNull('first_cfu_count')->orWhereNotNull('first_growth_result');
            })
            ->orderBy('id')
            ->each(function ($point): void {
                DB::table('microbiological_check_readings')->insert([
                    'microbiological_check_point_id' => $point->id,
                    'reading_number' => 1,
                    'cfu_count' => $point->first_cfu_count,
                    'growth_result' => $point->first_growth_result,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        DB::table('microbiological_check_points')
            ->where(function ($query): void {
                $query->whereNotNull('second_cfu_count')->orWhereNotNull('second_growth_result');
            })
            ->orderBy('id')
            ->each(function ($point): void {
                DB::table('microbiological_check_readings')->insert([
                    'microbiological_check_point_id' => $point->id,
                    'reading_number' => 2,
                    'cfu_count' => $point->second_cfu_count,
                    'growth_result' => $point->second_growth_result,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('microbiological_check_readings');
    }
};