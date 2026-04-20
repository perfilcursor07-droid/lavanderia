<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tipo;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar tipo especial "Peso" para coletas por peso
        Tipo::create([
            'nome' => 'Peso',
            'descricao' => 'Tipo especial para coletas realizadas por peso (kg)',
            'categoria' => 'peso',
            'ativo' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Tipo::where('nome', 'Peso')->where('categoria', 'peso')->delete();
    }
};
