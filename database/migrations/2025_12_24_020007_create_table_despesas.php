<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vps_id')->constrained('vps')->onDelete('cascade');
            $table->enum('tipo', ['compra', 'cobranca']); // Compra inicial ou mensalidade
            $table->decimal('valor', 10, 2);
            $table->string('descricao')->nullable();
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('status')->default('pendente'); // pendente, pago, atrasado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('despesas');
    }
};