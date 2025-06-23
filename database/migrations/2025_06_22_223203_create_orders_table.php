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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('coin_id')->constrained()->onDelete('cascade'); // Oferta asociada
            $table->decimal('usdt_amount', 18, 8); // Cantidad de USDT
            $table->decimal('local_amount', 18, 2); // Cantidad en moneda local
            $table->string('local_currency', 3)->default('USD');
            $table->string('voucher_path')->nullable(); // Ruta del comprobante de pago (opcional)
            $table->enum('type', ['buy', 'sell']); // Tipo de orden (compra/venta)
            $table->enum('status', ['pending', 'confirmed', 'proof_uploaded', 'queued', 'completed', 'cancelled', 'disputed'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
