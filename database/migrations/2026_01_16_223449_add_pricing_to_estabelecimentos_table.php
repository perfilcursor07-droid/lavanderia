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
        Schema::table('estabelecimentos', function (Blueprint $table) {
            // Tipo de precificação: 'peso' ou 'peca'
            $table->enum('tipo_precificacao', ['peso', 'peca'])->default('peso')->after('ativo');
            
            // Preço por kg (quando tipo_precificacao = 'peso')
            $table->decimal('preco_kg', 8, 2)->default(0)->after('tipo_precificacao');
            
            // Preço por peça (quando tipo_precificacao = 'peca')
            $table->decimal('preco_peca', 8, 2)->default(0)->after('preco_kg');
            
            // Observações sobre precificação
            $table->text('observacoes_preco')->nullable()->after('preco_peca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estabelecimentos', function (Blueprint $table) {
            $table->dropColumn(['tipo_precificacao', 'preco_kg', 'preco_peca', 'observacoes_preco']);
        });
    }
};
