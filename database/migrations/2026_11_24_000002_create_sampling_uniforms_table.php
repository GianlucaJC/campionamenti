<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sampling_uniforms', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('sampling_uniforms')->insert(array_map(
            fn (string $name): array => ['name' => $name, 'created_at' => $now, 'updated_at' => $now],
            ['Divisa bianca', 'Tuta bianca', 'Tuta blu', 'Camice']
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('sampling_uniforms');
    }
};
