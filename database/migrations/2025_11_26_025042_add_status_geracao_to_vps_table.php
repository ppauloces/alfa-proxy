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
        Schema::table('vps', function (Blueprint $table) {
            // Campo para rastrear status da geração de proxies
            // Valores: null (não vai gerar), pending, processing, completed, failed
            $table->string('status_geracao', 20)->nullable()->after('status');

            // Mensagem de erro caso a geração falhe
            $table->text('erro_geracao')->nullable()->after('status_geracao');

            // Contador de proxies geradas para esta VPS
            $table->integer('proxies_geradas')->default(0)->after('erro_geracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps', function (Blueprint $table) {
            $table->dropColumn(['status_geracao', 'erro_geracao', 'proxies_geradas']);
        });
    }
};
