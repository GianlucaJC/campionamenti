<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'sampling' => ['sampling_reopened_by_user_id', 'sampling_reopened_at', 'sampling_reopening_reason'],
            'reading_1' => ['first_reading_reopened_by_user_id', 'first_reading_reopened_at', 'first_reading_reopening_reason'],
            'reading_2' => ['second_reading_reopened_by_user_id', 'second_reading_reopened_at', 'second_reading_reopening_reason'],
        ] as $phase => [$reopenedBy, $reopenedAt, $reason]) {
            DB::table('microbiological_checks')
                ->whereNotNull($reopenedAt)
                ->orderBy('id')
                ->each(function ($check) use ($phase, $reopenedBy, $reopenedAt, $reason): void {
                    DB::table('microbiological_check_phase_states')
                        ->where('microbiological_check_id', $check->id)
                        ->where('phase', $phase)
                        ->update([
                            'reopened_by_user_id' => $check->{$reopenedBy},
                            'reopened_at' => $check->{$reopenedAt},
                            'reopening_reason' => $check->{$reason},
                            'updated_at' => now(),
                        ]);
                });
        }
    }

    public function down(): void
    {
    }
};