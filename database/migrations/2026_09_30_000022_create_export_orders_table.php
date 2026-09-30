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
        Schema::create('export_orders', function (Blueprint $table) {
            $table->id();
            $table->string('export_code')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->timestamp('order_date');
            $table->string('destination'); // Country / Port of Entry
            $table->decimal('total_amount', 14, 2);
            $table->timestamp('shipment_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->string('status')->default('Draft'); // Draft, Pending, Processing, Shipped, Delivered, Completed, Cancelled
            $table->string('created_by')->default('Export Manager');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_orders');
    }
};
