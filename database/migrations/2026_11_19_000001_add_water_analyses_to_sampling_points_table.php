<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sampling_points', function (Blueprint $table) {
            $table->json('water_analyses')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sampling_points', function (Blueprint $table) {
            $table->dropColumn('water_analyses');
        });
    }
};
