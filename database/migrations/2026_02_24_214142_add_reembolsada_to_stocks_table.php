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
            $table->boolean('reembolsada')->default(false)->after('substituido_por');
            $table->unsignedBigInteger('reembolsado_por')->nullable()->after('reembolsada');
            $table->timestamp('reembolsado_em')->nullable()->after('reembolsado_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['reembolsada', 'reembolsado_por', 'reembolsado_em']);
        });
    }
};
