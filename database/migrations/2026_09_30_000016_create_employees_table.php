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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('position');
            $table->string('contact_number');
            $table->string('address');
            $table->date('date_hired');
            $table->decimal('salary', 12, 2); // Basic Salary
            $table->string('employment_status')->default('Regular'); // Regular, Probationary, Contractual, Resigned, Terminated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

