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
            // Remover a foreign key existente
            $table->dropForeign(['user_id']);

            // Tornar user_id nullable
            $table->foreignId('user_id')->nullable()->change();

            // Tornar expiracao nullable
            $table->datetime('expiracao')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Reverter para NOT NULL
            $table->foreignId('user_id')->nullable(false)->change();
            $table->datetime('expiracao')->nullable(false)->change();

            // Recriar a foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
