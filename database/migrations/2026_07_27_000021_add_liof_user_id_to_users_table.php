<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedBigInteger('liof_user_id')->nullable()->after('id');
            $table->unique('liof_user_id', 'users_liof_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique('users_liof_user_id_unique');
            $table->dropColumn('liof_user_id');
        });
    }
};