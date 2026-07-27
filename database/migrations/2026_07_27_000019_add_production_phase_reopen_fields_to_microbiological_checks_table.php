<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->unsignedBigInteger('sampling_reopened_by_user_id')->nullable()->after('sampling_completed_by_user_id');
            $table->timestamp('sampling_reopened_at')->nullable()->after('sampling_reopened_by_user_id');
            $table->text('sampling_reopening_reason')->nullable()->after('sampling_reopened_at');
            $table->unsignedBigInteger('first_reading_reopened_by_user_id')->nullable()->after('first_reading_completed_by_user_id');
            $table->timestamp('first_reading_reopened_at')->nullable()->after('first_reading_reopened_by_user_id');
            $table->text('first_reading_reopening_reason')->nullable()->after('first_reading_reopened_at');
            $table->unsignedBigInteger('second_reading_reopened_by_user_id')->nullable()->after('second_reading_completed_by_user_id');
            $table->timestamp('second_reading_reopened_at')->nullable()->after('second_reading_reopened_by_user_id');
            $table->text('second_reading_reopening_reason')->nullable()->after('second_reading_reopened_at');

            $table->foreign('sampling_reopened_by_user_id', 'checks_sampling_reopen_fk')->references('id')->on('users')->nullOnDelete();
            $table->foreign('first_reading_reopened_by_user_id', 'checks_first_reading_reopen_fk')->references('id')->on('users')->nullOnDelete();
            $table->foreign('second_reading_reopened_by_user_id', 'checks_second_reading_reopen_fk')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropForeign('checks_sampling_reopen_fk');
            $table->dropForeign('checks_first_reading_reopen_fk');
            $table->dropForeign('checks_second_reading_reopen_fk');
            $table->dropColumn([
                'sampling_reopened_by_user_id',
                'sampling_reopened_at',
                'sampling_reopening_reason',
                'first_reading_reopened_by_user_id',
                'first_reading_reopened_at',
                'first_reading_reopening_reason',
                'second_reading_reopened_by_user_id',
                'second_reading_reopened_at',
                'second_reading_reopening_reason',
            ]);
        });
    }
};