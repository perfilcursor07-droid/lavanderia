<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coleta;

echo "Debug detalhado do cálculo...\n";

$coleta = Coleta::with(['estabelecimento', 'pesagens', 'pecas'])->where('numero_coleta', 'COL000003')->first();

if ($coleta) {
    echo "=== INFORMAÇÕES DA COLETA ===\n";
    echo "Coleta: {$coleta->numero_coleta}\n";
    echo "Estabelecimento: {$coleta->estabelecimento->razao_social}\n";
    echo "Tipo precificação: {$coleta->estabelecimento->tipo_precificacao}\n";
    echo "Preço kg: {$coleta->estabelecimento->preco_kg}\n";
    echo "Preço peça: {$coleta->estabelecimento->preco_peca}\n";
    
    echo "\n=== PEÇAS ===\n";
    $pesoTotalPecas = 0;
    $quantidadeTotal = 0;
    foreach ($coleta->pecas as $peca) {
        echo "Peça ID {$peca->id}: Peso = {$peca->peso} kg, Quantidade = {$peca->quantidade}\n";
        $pesoTotalPecas += $peca->peso;
        $quantidadeTotal += $peca->quantidade;
    }
    echo "Total peças: Peso = {$pesoTotalPecas} kg, Quantidade = {$quantidadeTotal}\n";
    
    echo "\n=== PESAGENS ===\n";
    $pesoTotalPesagens = 0;
    foreach ($coleta->pesagens as $pesagem) {
        echo "Pesagem ID {$pesagem->id}: Peso = {$pesagem->peso} kg\n";
        $pesoTotalPesagens += $pesagem->peso;
    }
    echo "Total pesagens: Peso = {$pesoTotalPesagens} kg\n";
    
    echo "\n=== CÁLCULOS ===\n";
    $pesoTotal = $pesoTotalPecas + $pesoTotalPesagens;
    echo "Peso total: {$pesoTotal} kg\n";
    
    $valorTotal = 0;
    
    // Valor das peças
    if ($coleta->estabelecimento->tipo_precificacao === 'peso') {
        $valorPecas = $pesoTotalPecas * $coleta->estabelecimento->preco_kg;
        echo "Valor peças (por peso): {$pesoTotalPecas} × {$coleta->estabelecimento->preco_kg} = {$valorPecas}\n";
    } elseif ($coleta->estabelecimento->tipo_precificacao === 'peca') {
        $valorPecas = $quantidadeTotal * $coleta->estabelecimento->preco_peca;
        echo "Valor peças (por peça): {$quantidadeTotal} × {$coleta->estabelecimento->preco_peca} = {$valorPecas}\n";
    }
    $valorTotal += $valorPecas;
    
    // Valor das pesagens
    $valorPesagens = $pesoTotalPesagens * $coleta->estabelecimento->preco_kg;
    echo "Valor pesagens: {$pesoTotalPesagens} × {$coleta->estabelecimento->preco_kg} = {$valorPesagens}\n";
    $valorTotal += $valorPesagens;
    
    echo "Valor total calculado: {$valorTotal}\n";
    
    echo "\n=== ATUALIZANDO NO BANCO ===\n";
    $coleta->peso_total = $pesoTotal;
    $coleta->valor_total = $valorTotal;
    $result = $coleta->save();
    
    echo "Resultado da atualização: " . ($result ? 'Sucesso' : 'Falha') . "\n";
    
    // Verificar se foi salvo
    $coleta->refresh();
    echo "Peso no banco: {$coleta->peso_total}\n";
    echo "Valor no banco: {$coleta->valor_total}\n";
    
} else {
    echo "Coleta não encontrada\n";
}

echo "Debug concluído!\n";