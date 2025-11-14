<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vps', function (Blueprint $table) {
            $table->id();
            $table->string('apelido')->nullable(); // Ex: BR-ALFA 01
            $table->string('ip');
            $table->string('usuario_ssh'); // Ex: root
            $table->string('senha_ssh');
            $table->decimal('valor', 10, 2)->nullable(); // Valor da VPS
            $table->string('pais');
            $table->string('hospedagem')->nullable(); // OVH, Hetzner, etc
            $table->integer('periodo_dias')->nullable(); // 30, 60, 90, 180
            $table->date('data_contratacao')->nullable();
            $table->string('status')->default('Operacional'); // Operacional, Manutenção, etc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vps');
    }
};