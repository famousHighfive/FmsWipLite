<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('planning_assignments', function (Blueprint $table) {
            $table->id();
            // Relations avec contraintes
            $table->foreignId('planning_model_id')->constrained('planning_models')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            // Dates de validité
            $table->date('start_date');
            $table->date('end_date');

            // Statut et validation
            $table->enum('status', ['en attente', 'validé', 'suspendu'])->default('en attente');
            $table->foreignId('validated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->timestamps(); // Gère created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planning_assignments');
    }
};
