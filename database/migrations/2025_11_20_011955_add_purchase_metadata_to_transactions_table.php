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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('metodo_pagamento')->nullable()->after('status');
            $table->string('tipo')->default('recarga')->after('metodo_pagamento'); // recarga, compra_proxy
            $table->json('metadata')->nullable()->after('tipo'); // Detalhes da compra (país, período, quantidade, motivo)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['metodo_pagamento', 'tipo', 'metadata']);
        });
    }
};
