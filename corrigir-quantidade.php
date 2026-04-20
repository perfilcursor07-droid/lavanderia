<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ColetaPeca;
use App\Models\Coleta;

echo "Corrigindo quantidade da peça...\n";

// Corrigir a peça específica
$peca = ColetaPeca::find(57);
if ($peca) {
    $peca->quantidade = 1;
    $peca->save();
    echo "Quantidade da peça ID 57 atualizada para 1\n";
    
    // Recalcular totais da coleta
    $coleta = $peca->coleta;
    if ($coleta) {
        $coleta->calcularTotais();
        echo "Totais recalculados:\n";
        echo "Peso total: {$coleta->peso_total} kg\n";
        echo "Valor total: R$ " . number_format($coleta->valor_total, 2, ',', '.') . "\n";
        echo "Estabelecimento: {$coleta->estabelecimento->razao_social}\n";
        echo "Tipo precificação: {$coleta->estabelecimento->tipo_precificacao}\n";
        echo "Preço por peça: R$ " . number_format($coleta->estabelecimento->preco_peca, 2, ',', '.') . "\n";
    }
} else {
    echo "Peça não encontrada\n";
}

echo "Correção concluída!\n";