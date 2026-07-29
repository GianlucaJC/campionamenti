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
        Schema::table('sampling_points', function (Blueprint $table) {
            $table->unsignedTinyInteger('default_exposure_hours')
                ->nullable()
                ->after('default_volume_liters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sampling_points', function (Blueprint $table) {
            $table->dropColumn('default_exposure_hours');
        });
    }
};