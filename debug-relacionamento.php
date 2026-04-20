<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coleta;

echo "Debugando relacionamento pesagens...\n";

$coleta = Coleta::with(['pesagens'])->where('numero_coleta', 'COL000003')->first();

if ($coleta) {
    echo "Coleta: {$coleta->numero_coleta}\n";
    echo "Número de pesagens: " . $coleta->pesagens->count() . "\n";
    
    foreach ($coleta->pesagens as $pesagem) {
        echo "Pesagem ID: {$pesagem->id}, Peso: {$pesagem->peso} kg\n";
    }
    
    // Testar o cálculo manual
    $valorPesagens = 0;
    foreach ($coleta->pesagens as $pesagem) {
        $valor = $pesagem->peso * $coleta->estabelecimento->preco_kg;
        $valorPesagens += $valor;
        echo "Pesagem {$pesagem->id}: {$pesagem->peso} × {$coleta->estabelecimento->preco_kg} = {$valor}\n";
    }
    
    echo "Total das pesagens: R$ " . number_format($valorPesagens, 2, ',', '.') . "\n";
    
} else {
    echo "Coleta não encontrada\n";
}

echo "Debug concluído!\n";