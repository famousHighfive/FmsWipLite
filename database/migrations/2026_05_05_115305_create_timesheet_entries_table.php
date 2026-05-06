<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('timesheet_entries', function (Blueprint $table) {
            $table->id();

            // Lien avec la feuille de temps
            $table->foreignId('timesheet_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Jour concerné
            $table->date('date');

            // Heures
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            // Pause (en minutes)
            $table->integer('break_duration')->default(0);

            // Heures
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->decimal('planned_hours', 5, 2)->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);

            // Absence
            $table->string('absence_type')->nullable();

            // Commentaire
            $table->text('comment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheet_entries');
    }
};

