<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            // L'employé concerné. Si on supprime l'employé de la base, son affectation s'efface (cascade)
            $table->foreignId('employee_id')->constrained('employees');

            // La campagne liée. Si on supprime la campagne, les affectations liées s'effacent aussi (cascade)
            $table->foreignId('campaign_id')->constrained('campaigns');

            // C'est un employé qui commande un autre employé.
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');

            // Le rôle dans la campagne 
            $table->foreignId('position_id')->constrained('positions');

            // État de l'affectation
            $table->enum('status', ['actif', 'terminé', 'suspendu'])->default('actif');
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
