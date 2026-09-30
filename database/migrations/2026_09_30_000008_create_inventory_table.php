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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('item_name');
            $table->string('category')->default('Fertilizer'); // Fertilizer, Chemicals, Raw Materials, etc.
            $table->string('item_type')->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit')->default('Bags');
            $table->decimal('reorder_level', 12, 2)->default(10);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('status')->default('In Stock'); // In Stock, Low Stock, Out of Stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
