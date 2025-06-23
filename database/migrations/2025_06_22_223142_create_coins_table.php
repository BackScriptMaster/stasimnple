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
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('buy_price', 18, 2); // Precio de compra de USDT en moneda local
            $table->decimal('sell_price', 18, 2); // Precio de venta de USDT en moneda local
            $table->string('local_currency', 3)->default('USD'); // Moneda local (e.g., USD)
            $table->decimal('min_amount', 18, 8)->nullable(); // Monto mínimo de transacción
            $table->decimal('max_amount', 18, 8)->nullable(); // Monto máximo de transacción
            $table->enum('status', ['active', 'inactive'])->default('active'); // Estado de la oferta

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins');
    }
};
