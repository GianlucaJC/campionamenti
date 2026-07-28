<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('microbiological_check_phase_states', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('microbiological_check_id');
            $table->string('phase', 30);
            $table->unsignedBigInteger('signed_by_user_id')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->unsignedBigInteger('reopened_by_user_id')->nullable();
            $table->timestamp('reopened_at')->nullable();
            $table->text('reopening_reason')->nullable();
            $table->timestamps();

            $table->foreign('microbiological_check_id', 'check_phase_state_check_fk')
                ->references('id')->on('microbiological_checks')->cascadeOnDelete();
            $table->foreign('signed_by_user_id', 'check_phase_state_signer_fk')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('reopened_by_user_id', 'check_phase_state_reopen_fk')
                ->references('id')->on('users')->nullOnDelete();
            $table->unique(['microbiological_check_id', 'phase'], 'check_phase_state_unique');
        });

        foreach ([
            'sampling' => 'sampling_completed_by_user_id',
            'reading_1' => 'first_reading_completed_by_user_id',
            'reading_2' => 'second_reading_completed_by_user_id',
        ] as $phase => $signerColumn) {
            DB::table('microbiological_checks')
                ->whereNotNull($signerColumn)
                ->orderBy('id')
                ->each(function ($check) use ($phase, $signerColumn): void {
                    DB::table('microbiological_check_phase_states')->insert([
                        'microbiological_check_id' => $check->id,
                        'phase' => $phase,
                        'signed_by_user_id' => $check->{$signerColumn},
                        'signed_at' => $check->updated_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('microbiological_check_phase_states');
    }
};