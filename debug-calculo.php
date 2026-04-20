<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coleta;

echo "Debugando cálculo de totais...\n";

$coleta = Coleta::with(['estabelecimento', 'pecas'])->where('numero_coleta', 'COL000003')->first();

if ($coleta) {
    echo "Coleta encontrada: {$coleta->numero_coleta}\n";
    echo "Estabelecimento: {$coleta->estabelecimento->razao_social}\n";
    echo "Tipo precificação: {$coleta->estabelecimento->tipo_precificacao}\n";
    echo "Preço kg: R$ " . number_format($coleta->estabelecimento->preco_kg, 2, ',', '.') . "\n";
    echo "Preço peça: R$ " . number_format($coleta->estabelecimento->preco_peca, 2, ',', '.') . "\n";
    
    echo "\nPeças da coleta:\n";
    foreach ($coleta->pecas as $peca) {
        echo "- ID: {$peca->id}, Quantidade: {$peca->quantidade}, Peso: {$peca->peso} kg\n";
    }
    
    $pesoTotal = $coleta->pecas->sum('peso');
    $quantidadeTotal = $coleta->pecas->sum('quantidade');
    
    echo "\nTotais calculados:\n";
    echo "Peso total: {$pesoTotal} kg\n";
    echo "Quantidade total: {$quantidadeTotal} peças\n";
    
    $valorTotal = 0;
    if ($coleta->estabelecimento->tipo_precificacao === 'peso') {
        $valorTotal = $pesoTotal * $coleta->estabelecimento->preco_kg;
        echo "Cálculo por peso: {$pesoTotal} × {$coleta->estabelecimento->preco_kg} = {$valorTotal}\n";
    } elseif ($coleta->estabelecimento->tipo_precificacao === 'peca') {
        $valorTotal = $quantidadeTotal * $coleta->estabelecimento->preco_peca;
        echo "Cálculo por peça: {$quantidadeTotal} × {$coleta->estabelecimento->preco_peca} = {$valorTotal}\n";
    }
    
    echo "Valor total calculado: R$ " . number_format($valorTotal, 2, ',', '.') . "\n";
    
    // Atualizar manualmente
    $coleta->valor_total = $valorTotal;
    $coleta->save();
    
    echo "Valor atualizado no banco de dados!\n";
} else {
    echo "Coleta não encontrada\n";
}

echo "Debug concluído!\n";