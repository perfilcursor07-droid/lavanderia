#!/bin/bash

echo "=== Deploy Fix Script ==="
echo "Atualizando autoload do Composer..."

# Atualizar autoload do composer
composer dump-autoload --optimize

echo "Limpando cache do Laravel..."

# Limpar todos os caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Recriando cache otimizado..."

# Recriar cache otimizado para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Verificando se as classes existem..."
php artisan tinker --execute="echo class_exists('Database\Seeders\NiveisAcessoSeeder') ? 'Seeder encontrado' : 'Seeder não encontrado';"

echo "=== Script concluído ==="