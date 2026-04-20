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
        Schema::table('empacotamento_pecas', function (Blueprint $table) {
            // Verificar se as colunas já não existem (algumas foram adicionadas em migration anterior)
            if (!Schema::hasColumn('empacotamento_pecas', 'status_saida')) {
                // Status de saída/entrega
                $table->enum('status_saida', ['pronto', 'em_transito', 'entregue'])->default('pronto')->after('observacoes');
            }
            
            if (!Schema::hasColumn('empacotamento_pecas', 'data_saida')) {
                // Dados de saída
                $table->timestamp('data_saida')->nullable()->after('status_saida');
            }
            
            if (!Schema::hasColumn('empacotamento_pecas', 'motorista_saida_id')) {
                $table->foreignId('motorista_saida_id')->nullable()->constrained('usuarios')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('empacotamento_pecas', 'data_entrega')) {
                // Dados de entrega
                $table->timestamp('data_entrega')->nullable();
            }
            
            if (!Schema::hasColumn('empacotamento_pecas', 'motorista_entrega_id')) {
                $table->foreignId('motorista_entrega_id')->nullable()->constrained('usuarios')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('empacotamento_pecas', 'nome_recebedor')) {
                $table->string('nome_recebedor')->nullable();
            }
            
            if (!Schema::hasColumn('empacotamento_pecas', 'assinatura_recebedor')) {
                $table->longText('assinatura_recebedor')->nullable();
            }
            
            // Nota: relave, inutilizada, impresso, data_impressao e responsavel_empacotamento_id
            // já foram adicionados pela migration 2025_09_05_140000_add_empacotamento_improvements.php
        });
        
        // Adicionar índices em uma operação separada para evitar conflitos
        Schema::table('empacotamento_pecas', function (Blueprint $table) {
            $indexExists = DB::select("SHOW INDEX FROM empacotamento_pecas WHERE Key_name = 'empacotamento_pecas_status_saida_index'");
            if (empty($indexExists)) {
                $table->index('status_saida');
            }
            
            $indexExists = DB::select("SHOW INDEX FROM empacotamento_pecas WHERE Key_name = 'empacotamento_pecas_motorista_saida_id_index'");
            if (empty($indexExists)) {
                $table->index('motorista_saida_id');
            }
            
            $indexExists = DB::select("SHOW INDEX FROM empacotamento_pecas WHERE Key_name = 'empacotamento_pecas_motorista_entrega_id_index'");
            if (empty($indexExists)) {
                $table->index('motorista_entrega_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empacotamento_pecas', function (Blueprint $table) {
            // Remover índices se existirem
            $indexes = ['status_saida', 'motorista_saida_id', 'motorista_entrega_id'];
            foreach ($indexes as $index) {
                $indexExists = DB::select("SHOW INDEX FROM empacotamento_pecas WHERE Key_name = 'empacotamento_pecas_{$index}_index'");
                if (!empty($indexExists)) {
                    $table->dropIndex("empacotamento_pecas_{$index}_index");
                }
            }
        });
        
        Schema::table('empacotamento_pecas', function (Blueprint $table) {
            // Remover foreign keys se existirem
            if (Schema::hasColumn('empacotamento_pecas', 'motorista_saida_id')) {
                $table->dropForeign(['motorista_saida_id']);
            }
            if (Schema::hasColumn('empacotamento_pecas', 'motorista_entrega_id')) {
                $table->dropForeign(['motorista_entrega_id']);
            }
            
            // Remover apenas as colunas que esta migration adicionou
            $columns = [
                'status_saida',
                'data_saida',
                'motorista_saida_id',
                'data_entrega',
                'motorista_entrega_id',
                'nome_recebedor',
                'assinatura_recebedor'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('empacotamento_pecas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

