<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->string('sampling_completed_signature')->nullable()->after('operator_name');
            $table->string('first_reading_completed_signature')->nullable()->after('sampling_completed_signature');
            $table->string('second_reading_completed_signature')->nullable()->after('first_reading_completed_signature');
        });
    }

    public function down(): void
    {
        Schema::table('microbiological_checks', function (Blueprint $table): void {
            $table->dropColumn([
                'sampling_completed_signature',
                'first_reading_completed_signature',
                'second_reading_completed_signature',
            ]);
        });
    }
};