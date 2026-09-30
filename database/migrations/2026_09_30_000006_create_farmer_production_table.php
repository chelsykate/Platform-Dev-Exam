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
        Schema::create('farmer_production', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('crop_year'); // e.g. 2024-2025
            $table->date('planting_date');
            $table->date('harvest_date')->nullable();
            $table->decimal('estimated_yield', 12, 2); // Metric tons
            $table->decimal('actual_yield', 12, 2)->nullable(); // Metric tons
            $table->string('status')->default('Planted'); // Planted, Growing, Harvested, Completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmer_production');
    }
};

