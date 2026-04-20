#!/bin/bash

echo "=== Executando Seeders em Produção ==="

# Atualizar autoload primeiro
composer dump-autoload --optimize

# Limpar cache
php artisan cache:clear
php artisan config:clear

echo "Rodando seeders individuais..."

# Rodar cada seeder individualmente
echo "1. Rodando NiveisAcessoSeeder..."
php artisan db:seed --class=NiveisAcessoSeeder --force

echo "2. Rodando StatusSeeder..."
php artisan db:seed --class=StatusSeeder --force

echo "3. Rodando TiposSeeder..."
php artisan db:seed --class=TiposSeeder --force

echo "4. Rodando UsuarioAdminSeeder..."
php artisan db:seed --class=UsuarioAdminSeeder --force

echo "=== Seeders concluídos ==="