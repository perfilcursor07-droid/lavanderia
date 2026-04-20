<?php
// Teste de logs - execute: php test_logs.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTANDO LOGS...\n\n";

// Testar diferentes níveis de log
\Log::emergency("🚨 TESTE EMERGENCY - " . date('Y-m-d H:i:s'));
\Log::alert("⚠️ TESTE ALERT - " . date('Y-m-d H:i:s'));
\Log::critical("❗ TESTE CRITICAL - " . date('Y-m-d H:i:s'));
\Log::error("❌ TESTE ERROR - " . date('Y-m-d H:i:s'));
\Log::warning("⚠️ TESTE WARNING - " . date('Y-m-d H:i:s'));
\Log::notice("📢 TESTE NOTICE - " . date('Y-m-d H:i:s'));
\Log::info("ℹ️ TESTE INFO - " . date('Y-m-d H:i:s'));
\Log::debug("🐛 TESTE DEBUG - " . date('Y-m-d H:i:s'));

echo "✅ Logs enviados! Verifique o arquivo storage/logs/laravel.log\n";
echo "Execute: tail -f storage/logs/laravel.log\n";
