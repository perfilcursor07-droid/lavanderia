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
        // Adicionar campos para melhorias no empacotamento_pecas
        Schema::table('empacotamento_pecas', function (Blueprint $table) {
            $table->boolean('relave')->default(false)->comment('Indica se a peça é um relave');
            $table->boolean('inutilizada')->default(false)->comment('Indica se a peça foi inutilizada');
            $table->boolean('impresso')->default(false)->comment('Indica se a etiqueta foi impressa');
            $table->timestamp('data_impressao')->nullable()->comment('Data e hora da impressão da etiqueta');
            $table->unsignedBigInteger('responsavel_empacotamento_id')->nullable()->comment('ID do usuário responsável pelo empacotamento desta peça');
            
            // Adicionar índices para performance
            $table->index(['relave', 'empacotamento_id']);
            $table->index(['inutilizada', 'empacotamento_id']);
            $table->index(['impresso', 'empacotamento_id']);
            $table->index('responsavel_empacotamento_id');
        });

        // Adicionar campos para melhorias no coleta_pecas
        Schema::table('coleta_pecas', function (Blueprint $table) {
            $table->boolean('relave')->default(false)->comment('Indica se a peça foi coletada como relave');
            $table->boolean('desengoma')->default(false)->comment('Indica se a peça é para desengoma');
            
            // Adicionar índices
            $table->index(['relave', 'coleta_id']);
            $table->index(['desengoma', 'coleta_id']);
        });

        // Adicionar campos para melhorias nas coletas
        Schema::table('coletas', function (Blueprint $table) {
            $table->enum('tipo_coleta', ['normal', 'desengoma', 'relave'])->default('normal')->comment('Tipo da coleta');
            $table->date('data_prazo_entrega')->nullable()->comment('Data limite para entrega (especialmente para desengoma)');
            
            // Adicionar índice
            $table->index('tipo_coleta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empacotamento_pecas', function (Blueprint $table) {
            $table->dropIndex(['relave', 'empacotamento_id']);
            $table->dropIndex(['inutilizada', 'empacotamento_id']);
            $table->dropIndex(['impresso', 'empacotamento_id']);
            $table->dropIndex(['responsavel_empacotamento_id']);
            
            $table->dropColumn([
                'relave',
                'inutilizada', 
                'impresso',
                'data_impressao',
                'responsavel_empacotamento_id'
            ]);
        });

        Schema::table('coleta_pecas', function (Blueprint $table) {
            $table->dropIndex(['relave', 'coleta_id']);
            $table->dropIndex(['desengoma', 'coleta_id']);
            
            $table->dropColumn(['relave', 'desengoma']);
        });

        Schema::table('coletas', function (Blueprint $table) {
            $table->dropIndex(['tipo_coleta']);
            $table->dropColumn(['tipo_coleta', 'data_prazo_entrega']);
        });
    }
};
