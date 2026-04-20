#!/bin/bash

echo "🚀 DEPLOY LAVANDERIA - PRODUÇÃO"
echo "================================"

# 0. Atualizar código do Git
echo "📥 Atualizando código..."
git reset --hard HEAD
git pull origin main

# 1. Limpar todos os caches primeiro
echo "📝 Limpando caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

# 2. Executar migrations (se houver)
echo "🗄️ Executando migrations..."
php artisan migrate --force

# 3. Recriar caches otimizados
echo "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 4. Verificar permissões
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 5. Recarregar serviços (detectar versão automaticamente)
echo "🔄 Recarregando serviços..."

# Detectar versão do PHP
PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
echo "Versão do PHP detectada: $PHP_VERSION"

# Tentar recarregar PHP-FPM
if systemctl is-active --quiet php${PHP_VERSION}-fpm; then
    echo "Recarregando PHP ${PHP_VERSION}-FPM..."
    systemctl reload php${PHP_VERSION}-fpm
elif systemctl is-active --quiet php-fpm; then
    echo "Recarregando PHP-FPM..."
    systemctl reload php-fpm
fi

# Recarregar servidor web
if systemctl is-active --quiet nginx; then
    echo "Recarregando Nginx..."
    systemctl reload nginx
elif systemctl is-active --quiet apache2; then
    echo "Recarregando Apache..."
    systemctl reload apache2
fi

echo "✅ Deploy concluído!"
echo "🌐 Teste as alterações no navegador"
