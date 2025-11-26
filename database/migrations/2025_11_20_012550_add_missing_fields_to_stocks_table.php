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
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('motivo_uso')->nullable()->after('pais'); // Motivo de uso (Facebook, Google, etc)
            $table->integer('periodo_dias')->nullable()->after('motivo_uso'); // Período contratado em dias
            $table->boolean('renovacao_automatica')->default(false)->after('disponibilidade');
            $table->string('codigo_pais')->nullable()->after('pais'); // Código do país (BR, US, etc)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['motivo_uso', 'periodo_dias', 'renovacao_automatica', 'codigo_pais']);
        });
    }
};
