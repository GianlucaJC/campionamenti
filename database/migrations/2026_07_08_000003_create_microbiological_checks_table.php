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
        Schema::create('microbiological_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_section_id')->constrained()->cascadeOnDelete();
            $table->date('sampled_on');
            $table->date('incubation_started_on')->nullable();
            $table->date('first_reading_on')->nullable();
            $table->date('second_reading_on')->nullable();
            $table->string('operator_name')->nullable();
            $table->string('cq_operator_name')->nullable();
            $table->string('media_lot')->nullable();
            $table->string('swab_lot')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['monitoring_section_id', 'sampled_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microbiological_checks');
    }
};
