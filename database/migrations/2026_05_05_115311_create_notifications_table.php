<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Laravel utilise par défaut des UUID pour les notifications
            $table->uuid('id')->primary();
            $table->string('type'); // Le nom de la classe Notification

            // Cible de la notification (User ou Employee)
            $table->morphs('notifiable');

            // Les données sont stockées en JSON (titre, message, id de l'objet concerné)
            $table->json('data');

            $table->timestamp('read_at')->nullable(); // Gestion du statut "lu/non-lu"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
