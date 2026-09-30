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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->cascadeOnDelete();
            $table->string('transaction_type'); // STOCK_IN, STOCK_OUT, FERTILIZER_RELEASE, ADJUSTMENT, TRANSFER
            $table->decimal('quantity', 12, 2);
            $table->string('reference_type')->nullable(); // e.g. FertilizerDistribution, SalesOrder, ImportOrder
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamp('transaction_date');
            $table->string('performed_by')->default('System');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
