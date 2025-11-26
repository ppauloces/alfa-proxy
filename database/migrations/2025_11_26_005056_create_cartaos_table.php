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
        Schema::create('cartaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('bandeira', 20); // visa, mastercard, amex, elo, etc
            $table->string('ultimos_digitos', 4);
            $table->integer('mes_expiracao');
            $table->integer('ano_expiracao');
            $table->string('nome_titular');
            $table->string('gateway')->nullable(); // stripe, asaas, etc
            $table->text('token_gateway1')->nullable(); // Token do Stripe
            $table->text('token_gateway2')->nullable(); // Token do Asaas
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartaos');
    }
};
