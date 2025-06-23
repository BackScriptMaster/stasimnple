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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('usdt_balance', 18, 8)->default(0); // Saldo en USDT
            $table->decimal('local_balance', 18, 2)->default(0); // Saldo en moneda local (e.g., USD)
            $table->string('local_currency', 3)->default('USD'); // Código de moneda local (ISO 4217)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
