<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->timestamp('recycling_notified_at')->nullable()->after('reembolsado_em');
            $table->timestamp('recycled_at')->nullable()->after('recycling_notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['recycling_notified_at', 'recycled_at']);
        });
    }
};
