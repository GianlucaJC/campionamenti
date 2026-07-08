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
        Schema::create('sampling_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_section_id')->constrained()->cascadeOnDelete();
            $table->string('legacy_code')->nullable()->index();
            $table->string('title');
            $table->string('area_label')->nullable();
            $table->string('sample_kind')->default('air_active');
            $table->unsignedInteger('default_volume_liters')->nullable();
            $table->boolean('requires_operational_status')->default(true);
            $table->boolean('requires_product_lot')->default(true);
            $table->decimal('sort_order', 8, 3)->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_points');
    }
};
