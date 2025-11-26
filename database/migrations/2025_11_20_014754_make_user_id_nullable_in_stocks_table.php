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
            // Remover a foreign key constraint primeiro
            $table->dropForeign(['user_id']);

            // Tornar a coluna nullable
            $table->foreignId('user_id')->nullable()->change();

            // Recriar a foreign key com cascade
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Remover a foreign key
            $table->dropForeign(['user_id']);

            // Tornar NOT NULL novamente
            $table->foreignId('user_id')->nullable(false)->change();

            // Recriar a foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
