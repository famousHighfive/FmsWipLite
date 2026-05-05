<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();

            // Employé concerné
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            // Période
            $table->date('period_start');
            $table->date('period_end');

            // Statut
            $table->enum('status', ['brouillon', 'soumis', 'valide'])
                  ->default('brouillon');

            // Validation
            $table->foreignId('validated_by')

                  ->constrained('employees')
                  ->nullOnDelete();

            $table->timestamp('validated_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
