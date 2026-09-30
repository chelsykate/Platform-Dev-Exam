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
        Schema::create('fertilizer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique();
            $table->foreignId('farmer_id')->constrained('farmers')->cascadeOnDelete();
            $table->foreignId('fertilizer_id')->constrained('fertilizers')->cascadeOnDelete();
            $table->decimal('requested_quantity', 12, 2);
            $table->decimal('approved_quantity', 12, 2)->nullable();
            $table->timestamp('request_date');
            $table->timestamp('approval_date')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('status')->default('Pending'); // Pending, Under Review, Approved, Rejected, Ready for Release, Released, Cancelled
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fertilizer_requests');
    }
};
