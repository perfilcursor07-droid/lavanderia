<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Pesagem;

echo "Testando cálculo de pesagem...\n";

// Buscar a pesagem específica
$pesagem = Pesagem::with(['coleta.estabelecimento'])->find(27);

if ($pesagem) {
    echo "Pesagem ID: {$pesagem->id}\n";
    echo "Peso: {$pesagem->peso} kg\n";
    echo "Quantidade: {$pesagem->quantidade} peças\n";
    echo "Estabelecimento: {$pesagem->coleta->estabelecimento->razao_social}\n";
    echo "Preço por kg: R$ " . number_format($pesagem->coleta->estabelecimento->preco_kg, 2, ',', '.') . "\n";
    echo "Preço por peça: R$ " . number_format($pesagem->coleta->estabelecimento->preco_peca, 2, ',', '.') . "\n";
    
    // Calcular valor correto (sempre por peso para pesagem)
    $valorCorreto = $pesagem->peso * $pesagem->coleta->estabelecimento->preco_kg;
    echo "\nCálculo correto para pesagem:\n";
    echo "{$pesagem->peso} kg × R$ " . number_format($pesagem->coleta->estabelecimento->preco_kg, 2, ',', '.') . "/kg = R$ " . number_format($valorCorreto, 2, ',', '.') . "\n";
    
    echo "\nValor calculado pelo model: {$pesagem->valor_formatado}\n";
    
} else {
    echo "Pesagem não encontrada\n";
}

echo "Teste concluído!\n";