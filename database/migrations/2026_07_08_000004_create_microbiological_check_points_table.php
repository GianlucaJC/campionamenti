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
        Schema::create('microbiological_check_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('microbiological_check_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sampling_point_id')->constrained()->restrictOnDelete();
            $table->time('sampled_at')->nullable();
            $table->boolean('is_operational')->nullable();
            $table->string('product_lot')->nullable();
            $table->unsignedInteger('cfu_count')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['microbiological_check_id', 'sampling_point_id'], 'micro_check_point_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microbiological_check_points');
    }
};
