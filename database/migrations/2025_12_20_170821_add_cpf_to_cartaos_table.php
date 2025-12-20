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
        Schema::table('cartaos', function (Blueprint $table) {
            $table->string('cpf', 14)->after('nome_titular')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cartaos', function (Blueprint $table) {
            $table->dropColumn('cpf');
        });
    }
};
