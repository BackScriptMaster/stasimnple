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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('loggable'); // Relación polimórfica (para Balance, Order, Coin, etc.)
            $table->string('action'); // Ejemplo: 'created', 'updated', 'deleted'
            $table->text('description')->nullable(); // Detalles de la acción
            $table->json('changes')->nullable(); // Cambios realizados (antes/después)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
