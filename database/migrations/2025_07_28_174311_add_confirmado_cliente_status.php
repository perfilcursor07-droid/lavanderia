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
        // Adicionar novo status "Confirmado pelo Cliente"
        \App\Models\Status::create([
            'nome' => 'Confirmado pelo Cliente',
            'descricao' => 'Entrega foi confirmada e assinada pelo cliente',
            'tipo' => 'empacotamento',
            'cor' => '#198754',
            'ordem' => 6,
            'ativo' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Status::where('nome', 'Confirmado pelo Cliente')->delete();
    }
};
