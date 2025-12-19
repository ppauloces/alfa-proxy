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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('gateway_transaction_id')->nullable()->after('transacao');
            $table->string('payment_method')->nullable()->after('gateway_transaction_id'); // pix, credit_card, boleto
            $table->foreignId('card_id')->nullable()->after('payment_method')->constrained('cartaos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['card_id']);
            $table->dropColumn(['gateway_transaction_id', 'payment_method', 'card_id']);
        });
    }
};
