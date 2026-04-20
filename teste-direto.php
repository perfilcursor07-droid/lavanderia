<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coleta;

echo "Teste direto do cálculo...\n";

$coleta = Coleta::with(['estabelecimento', 'pesagens', 'pecas'])->where('numero_coleta', 'COL000003')->first();

if ($coleta) {
    echo "Coleta: {$coleta->numero_coleta}\n";
    
    // Calcular manualmente
    $pesoTotal = $coleta->pecas->sum('peso');
    $valorTotal = 0;

    // Valor das peças
    if ($coleta->estabelecimento->tipo_precificacao === 'peca') {
        $quantidadeTotal = $coleta->pecas->sum('quantidade');
        $valorPecas = $quantidadeTotal * $coleta->estabelecimento->preco_peca;
        $valorTotal += $valorPecas;
        echo "Valor das peças: {$quantidadeTotal} × {$coleta->estabelecimento->preco_peca} = {$valorPecas}\n";
    }

    // Valor das pesagens
    $valorPesagens = 0;
    foreach ($coleta->pesagens as $pesagem) {
        $valorPesagem = $pesagem->peso * $coleta->estabelecimento->preco_kg;
        $valorPesagens += $valorPesagem;
        echo "Pesagem {$pesagem->id}: {$pesagem->peso} × {$coleta->estabelecimento->preco_kg} = {$valorPesagem}\n";
    }
    $valorTotal += $valorPesagens;

    echo "Valor total calculado: R$ " . number_format($valorTotal, 2, ',', '.') . "\n";
    
    // Atualizar diretamente
    $coleta->valor_total = $valorTotal;
    $coleta->save();
    
    echo "Valor atualizado no banco!\n";
    
} else {
    echo "Coleta não encontrada\n";
}

echo "Teste concluído!\n";