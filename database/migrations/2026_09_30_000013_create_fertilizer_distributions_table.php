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
        Schema::create('fertilizer_distributions', function (Blueprint $table) {
            $table->id();
            $table->string('distribution_code')->unique();
            $table->foreignId('request_id')->constrained('fertilizer_requests')->cascadeOnDelete();
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnDelete();
            $table->foreignId('fertilizer_id')->constrained('fertilizers')->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->timestamp('distribution_date');
            $table->string('released_by');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fertilizer_distributions');
    }
};
