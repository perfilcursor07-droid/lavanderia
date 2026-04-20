<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar status "Pronto para entrega" para "Pronto para motorista"
        Status::where('nome', 'Pronto para entrega')
              ->update([
                  'nome' => 'Pronto para motorista',
                  'descricao' => 'Empacotamento concluído, aguardando confirmação do motorista para saída'
              ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o status anterior
        Status::where('nome', 'Pronto para motorista')
              ->update([
                  'nome' => 'Pronto para entrega',
                  'descricao' => 'Empacotamento concluído, pronto para ser entregue'
              ]);
    }
};
