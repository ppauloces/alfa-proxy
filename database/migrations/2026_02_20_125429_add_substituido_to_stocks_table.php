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
            $table->boolean('substituido')->default(false)->after('bloqueada');
            $table->unsignedBigInteger('substituido_por')->nullable()->after('substituido');
            $table->foreign('substituido_por')->references('id')->on('stocks')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['substituido_por']);
            $table->dropColumn(['substituido', 'substituido_por']);
        });
    }
};
