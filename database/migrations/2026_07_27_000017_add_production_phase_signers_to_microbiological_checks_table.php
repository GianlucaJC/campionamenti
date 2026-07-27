<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addSamplingSigner = ! Schema::hasColumn('microbiological_checks', 'sampling_completed_by_user_id');
        $addFirstReadingSigner = ! Schema::hasColumn('microbiological_checks', 'first_reading_completed_by_user_id');
        $addSecondReadingSigner = ! Schema::hasColumn('microbiological_checks', 'second_reading_completed_by_user_id');

        Schema::table('microbiological_checks', function (Blueprint $table) use ($addSamplingSigner, $addFirstReadingSigner, $addSecondReadingSigner): void {
            if ($addSamplingSigner) {
                $table->foreignId('sampling_completed_by_user_id')->nullable()->after('sampling_completed_signature');
            }

            if ($addFirstReadingSigner) {
                $table->foreignId('first_reading_completed_by_user_id')->nullable()->after('first_reading_completed_signature');
            }

            if ($addSecondReadingSigner) {
                $table->foreignId('second_reading_completed_by_user_id')->nullable()->after('second_reading_completed_signature');
            }
        });

        $addSamplingSignerForeignKey = ! $this->foreignKeyName('sampling_completed_by_user_id');
        $addFirstReadingSignerForeignKey = ! $this->foreignKeyName('first_reading_completed_by_user_id');
        $addSecondReadingSignerForeignKey = ! $this->foreignKeyName('second_reading_completed_by_user_id');

        Schema::table('microbiological_checks', function (Blueprint $table) use ($addSamplingSignerForeignKey, $addFirstReadingSignerForeignKey, $addSecondReadingSignerForeignKey): void {
            if ($addSamplingSignerForeignKey) {
                $table->foreign('sampling_completed_by_user_id', 'checks_sampling_signer_fk')
                    ->references('id')->on('users')->nullOnDelete();
            }

            if ($addFirstReadingSignerForeignKey) {
                $table->foreign('first_reading_completed_by_user_id', 'checks_first_reading_signer_fk')
                    ->references('id')->on('users')->nullOnDelete();
            }

            if ($addSecondReadingSignerForeignKey) {
                $table->foreign('second_reading_completed_by_user_id', 'checks_second_reading_signer_fk')
                    ->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        $foreignKeys = array_filter([
            $this->foreignKeyName('sampling_completed_by_user_id'),
            $this->foreignKeyName('first_reading_completed_by_user_id'),
            $this->foreignKeyName('second_reading_completed_by_user_id'),
        ]);

        Schema::table('microbiological_checks', function (Blueprint $table) use ($foreignKeys): void {
            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey);
            }

            $table->dropColumn([
                'sampling_completed_by_user_id',
                'first_reading_completed_by_user_id',
                'second_reading_completed_by_user_id',
            ]);
        });
    }

    private function foreignKeyName(string $column): ?string
    {
        $foreignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'microbiological_checks')
            ->where('COLUMN_NAME', $column)
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        return is_string($foreignKey) ? $foreignKey : null;
    }
};