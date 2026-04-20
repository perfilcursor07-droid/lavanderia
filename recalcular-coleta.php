<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coleta;

echo "Recalculando totais da coleta COL000003...\n";

$coleta = Coleta::with(['estabelecimento', 'pecas', 'pesagens'])->where('numero_coleta', 'COL000003')->first();

if ($coleta) {
    echo "Coleta encontrada: {$coleta->numero_coleta}\n";
    echo "Estabelecimento: {$coleta->estabelecimento->razao_social}\n";
    echo "Preço por kg: R$ " . number_format($coleta->estabelecimento->preco_kg, 2, ',', '.') . "\n";
    
    echo "\nPeças da coleta:\n";
    $pesoTotalPecas = 0;
    $quantidadeTotalPecas = 0;
    foreach ($coleta->pecas as $peca) {
        echo "- Peso: {$peca->peso} kg, Quantidade: {$peca->quantidade}\n";
        $pesoTotalPecas += $peca->peso;
        $quantidadeTotalPecas += $peca->quantidade;
    }
    
    echo "\nPesagens da coleta:\n";
    $pesoTotalPesagens = 0;
    $valorTotalPesagens = 0;
    foreach ($coleta->pesagens as $pesagem) {
        $valorPesagem = $pesagem->peso * $coleta->estabelecimento->preco_kg;
        echo "- Peso: {$pesagem->peso} kg, Valor: R$ " . number_format($valorPesagem, 2, ',', '.') . "\n";
        $pesoTotalPesagens += $pesagem->peso;
        $valorTotalPesagens += $valorPesagem;
    }
    
    echo "\nTotais antes do recálculo:\n";
    echo "Peso total: {$coleta->peso_total} kg\n";
    echo "Valor total: R$ " . number_format($coleta->valor_total, 2, ',', '.') . "\n";
    
    // Recalcular
    $coleta->calcularTotais();
    $coleta->refresh();
    
    echo "\nTotais após recálculo:\n";
    echo "Peso total: {$coleta->peso_total} kg\n";
    echo "Valor total: R$ " . number_format($coleta->valor_total, 2, ',', '.') . "\n";
    
    echo "\nDetalhamento do cálculo:\n";
    echo "Valor das pesagens: R$ " . number_format($valorTotalPesagens, 2, ',', '.') . "\n";
    if ($coleta->estabelecimento->tipo_precificacao === 'peca') {
        $valorPecas = $quantidadeTotalPecas * $coleta->estabelecimento->preco_peca;
        echo "Valor das peças: {$quantidadeTotalPecas} × R$ " . number_format($coleta->estabelecimento->preco_peca, 2, ',', '.') . " = R$ " . number_format($valorPecas, 2, ',', '.') . "\n";
    } else {
        $valorPecas = $pesoTotalPecas * $coleta->estabelecimento->preco_kg;
        echo "Valor das peças: {$pesoTotalPecas} kg × R$ " . number_format($coleta->estabelecimento->preco_kg, 2, ',', '.') . " = R$ " . number_format($valorPecas, 2, ',', '.') . "\n";
    }
    
} else {
    echo "Coleta não encontrada\n";
}

echo "Recálculo concluído!\n";