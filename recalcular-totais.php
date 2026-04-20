<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coleta;

echo "Recalculando totais das coletas...\n";

$coletas = Coleta::all();

foreach ($coletas as $coleta) {
    $coleta->calcularTotais();
    echo "Coleta {$coleta->numero_coleta}: Peso = {$coleta->peso_total} kg, Valor = R$ " . number_format($coleta->valor_total, 2, ',', '.') . "\n";
}

echo "Totais recalculados com sucesso!\n";