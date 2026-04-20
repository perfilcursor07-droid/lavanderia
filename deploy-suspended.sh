#!/bin/bash

# Script para ativar página de suspensão no servidor de produção
# Execute este script no servidor: bash deploy-suspended.sh

echo "=========================================="
echo "ATIVANDO PÁGINA DE SUSPENSÃO"
echo "=========================================="

# Ir para o diretório do projeto
cd /home/lavanderia/htdocs/212lavanderia.com.br/public/lavanderia

# Fazer backup do .htaccess atual
echo "Fazendo backup do .htaccess..."
cp public/.htaccess public/.htaccess.backup

# Puxar as atualizações do Git
echo "Puxando atualizações do Git..."
git pull origin main

# Verificar se os arquivos foram baixados
echo ""
echo "Verificando arquivos de suspensão..."
if [ -f "public/suspended.html" ]; then
    echo "✓ suspended.html encontrado"
else
    echo "✗ suspended.html NÃO encontrado"
fi

if [ -f "public/index_suspended.html" ]; then
    echo "✓ index_suspended.html encontrado"
else
    echo "✗ index_suspended.html NÃO encontrado"
fi

# Limpar cache do Laravel
echo ""
echo "Limpando cache do Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "=========================================="
echo "SITE SUSPENSO COM SUCESSO!"
echo "=========================================="
echo ""
echo "Acesse: https://212lavanderia.com.br"
echo "Você será redirecionado para: https://212lavanderia.com.br/suspended.html"
echo ""
echo "Para DESATIVAR a suspensão:"
echo "1. Edite o arquivo public/.htaccess"
echo "2. Comente as linhas entre os marcadores SUSPEND SITE"
echo "3. Ou restaure o backup: cp public/.htaccess.backup public/.htaccess"
echo ""
