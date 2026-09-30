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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnDelete();
            $table->string('farm_name');
            $table->string('location');
            $table->decimal('farm_size', 10, 2);
            $table->string('farm_size_unit')->default('Hectares');
            $table->string('soil_type')->default('Loam');
            $table->string('status')->default('Active'); // Active, Fallow, Under Harvest, Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};

