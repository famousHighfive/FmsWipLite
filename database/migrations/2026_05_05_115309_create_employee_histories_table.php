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
        Schema::create('employee_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('old_position_id')->nullable()->constrained('positions');
            $table->foreignId('new_position_id')->nullable()->constrained('positions');
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->foreignId('changed_by')->constrained('users'); // ID de l'utilisateur
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_histories');
    }
};
