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
        Schema::table('empacotamento', function (Blueprint $table) {
            $table->foreignId('motorista_saida_id')->nullable()->constrained('usuarios')->after('motorista_id');
            $table->foreignId('motorista_entrega_id')->nullable()->constrained('usuarios')->after('motorista_saida_id');
            $table->text('assinatura_recebedor')->nullable()->after('nome_recebedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empacotamento', function (Blueprint $table) {
            $table->dropForeign(['motorista_saida_id']);
            $table->dropForeign(['motorista_entrega_id']);
            $table->dropColumn(['motorista_saida_id', 'motorista_entrega_id', 'assinatura_recebedor']);
        });
    }
};
