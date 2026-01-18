<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alterar o enum para incluir 'renovacao'
        DB::statement("ALTER TABLE despesas MODIFY COLUMN tipo ENUM('compra', 'cobranca', 'renovacao')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE despesas MODIFY COLUMN tipo ENUM('compra', 'cobranca')");
    }
};
