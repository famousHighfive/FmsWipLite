<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // Lien vers l'utilisateur qui a fait l'action
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('action'); // create, update, delete, login...

            // On utilise morphs pour model_type et model_id (ex: App\Models\Employee, id)
            $table->nullableMorphs('model');

            $table->text('description')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps(); // created_at servira de date d'action
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
