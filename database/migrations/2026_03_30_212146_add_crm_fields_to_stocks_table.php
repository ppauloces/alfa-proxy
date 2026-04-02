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
            $table->string('origem')->nullable()->after('renovacao_automatica'); // null = interno, 'crm' = via CRM
            $table->string('crm_referencia')->nullable()->after('origem'); // ID do usuario no CRM externo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['origem', 'crm_referencia']);
        });
    }
};
