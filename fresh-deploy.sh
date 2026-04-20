#!/bin/bash

echo "🚀 DEPLOY COMPLETO DO ZERO - LAVANDERIA"
echo "======================================="
echo "⚠️  ATENÇÃO: Isso vai APAGAR TUDO e reinstalar do Git!"
echo ""

# Confirmar ação
read -p "Tem certeza que deseja continuar? (digite 'SIM' para confirmar): " confirmacao
if [ "$confirmacao" != "SIM" ]; then
    echo "❌ Deploy cancelado."
    exit 1
fi

echo ""
echo "🗂️  Fazendo backup do .env atual..."
if [ -f ".env" ]; then
    cp .env .env.backup
    echo "✅ Backup do .env salvo como .env.backup"
fi

echo ""
echo "🗑️  Removendo arquivos atuais (exceto .env)..."
find . -maxdepth 1 ! -name '.' ! -name '..' ! -name '.env' ! -name '.env.backup' ! -name 'fresh-deploy.sh' -exec rm -rf {} +

echo ""
echo "📥 Clonando projeto do Git..."
git clone https://github.com/erickafram/lavanderia.git temp_lavanderia
mv temp_lavanderia/* .
mv temp_lavanderia/.* . 2>/dev/null || true
rm -rf temp_lavanderia

echo ""
echo "⚙️  Restaurando configurações..."
if [ -f ".env.backup" ]; then
    mv .env.backup .env
    echo "✅ Arquivo .env restaurado"
else
    echo "⚠️  Criando .env básico..."
    cp .env.example .env
    echo "📝 Configure o .env com suas credenciais do banco!"
fi

echo ""
echo "📦 Instalando dependências do Composer..."
composer install --no-dev --optimize-autoloader

echo ""
echo "🔑 Gerando chave da aplicação..."
php artisan key:generate --force

echo ""
echo "🗄️  Configurando banco de dados..."
echo "📋 Executando migrations..."
php artisan migrate:fresh --force

echo ""
echo "🌱 Inserindo dados básicos..."
php artisan db:seed --force

echo ""
echo "👤 Verificando usuário administrador..."
echo "ℹ️  Usuário admin já foi criado pelo seeder"

echo ""
echo "🔗 Criando link simbólico para storage..."
php artisan storage:link

echo ""
echo "🧹 Limpando caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear

echo ""
echo "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo ""
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
chmod 644 .env

echo ""
echo "🔄 Recarregando serviços..."

# Detectar versão do PHP
PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
echo "Versão do PHP detectada: $PHP_VERSION"

# Recarregar PHP-FPM
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

echo ""
echo "✅ DEPLOY COMPLETO FINALIZADO!"
echo "================================"
echo "🌐 Acesse: http://seu-dominio.com"
echo "👤 Login: admin@lavanderia.com"
echo "🔑 Senha: admin123"
echo ""
echo "📝 Não esqueça de:"
echo "   1. Verificar as configurações do .env"
echo "   2. Configurar o banco de dados se necessário"
echo "   3. Testar todas as funcionalidades"
echo ""
