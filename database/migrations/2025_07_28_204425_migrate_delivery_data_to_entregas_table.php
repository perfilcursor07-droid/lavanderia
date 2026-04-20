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
        // Migrar dados de entrega existentes do empacotamento para a tabela entregas
        $empacotamentos = DB::table('empacotamento')
            ->whereNotNull('data_entrega')
            ->orWhereNotNull('data_saida')
            ->orWhereNotNull('motorista_saida_id')
            ->orWhereNotNull('motorista_entrega_id')
            ->get();

        foreach ($empacotamentos as $empacotamento) {
            // Determinar status da entrega
            $statusId = $empacotamento->status_id;

            DB::table('entregas')->insert([
                'empacotamento_id' => $empacotamento->id,
                'motorista_saida_id' => $empacotamento->motorista_saida_id,
                'motorista_entrega_id' => $empacotamento->motorista_entrega_id,
                'status_id' => $statusId,
                'data_saida' => $empacotamento->data_saida,
                'data_entrega' => $empacotamento->data_entrega,
                'data_confirmacao_recebimento' => $empacotamento->data_confirmacao_recebimento,
                'nome_recebedor' => $empacotamento->nome_recebedor,
                'assinatura_recebedor' => $empacotamento->assinatura_recebedor,
                'assinatura_cliente' => $empacotamento->assinatura_recebimento,
                'observacoes_entrega' => $empacotamento->observacoes_entrega,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover todos os registros de entrega
        DB::table('entregas')->truncate();
    }
};
