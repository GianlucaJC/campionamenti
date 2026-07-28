<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_departments', function (Blueprint $table): void {
            $table->unsignedTinyInteger('readings_count')->default(2)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_departments', function (Blueprint $table): void {
            $table->dropColumn('readings_count');
        });
    }
};