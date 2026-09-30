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
        Schema::create('import_orders', function (Blueprint $table) {
            $table->id();
            $table->string('import_code')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->timestamp('order_date');
            $table->timestamp('expected_arrival');
            $table->timestamp('actual_arrival')->nullable();
            $table->decimal('total_cost', 14, 2);
            $table->string('status')->default('Draft'); // Draft, Pending, Processing, Arriving, Completed, Cancelled
            $table->string('created_by')->default('Trade Specialist');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_orders');
    }
};
