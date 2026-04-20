#!/bin/bash

# Script de Deploy para CloudPanel
# Sistema de Gestão de Lavanderia

echo "=========================================="
echo "Deploy - Sistema de Gestão de Lavanderia"
echo "=========================================="
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}Erro: Execute este script no diretório raiz do Laravel${NC}"
    exit 1
fi

echo -e "${YELLOW}[1/10] Copiando arquivo .env de produção...${NC}"
if [ -f ".env.production" ]; then
    cp .env.production .env
    echo -e "${GREEN}✓ Arquivo .env configurado${NC}"
else
    echo -e "${RED}✗ Arquivo .env.production não encontrado${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}[2/10] Instalando dependências do Composer...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✓ Dependências instaladas${NC}"

echo ""
echo -e "${YELLOW}[3/10] Configurando permissões...${NC}"
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}✓ Permissões configuradas${NC}"

echo ""
echo -e "${YELLOW}[4/10] Limpando caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✓ Caches limpos${NC}"

echo ""
echo -e "${YELLOW}[5/10] Executando migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Migrations executadas${NC}"

echo ""
echo -e "${YELLOW}[6/10] Executando seeders...${NC}"
php artisan db:seed --force
echo -e "${GREEN}✓ Seeders executados${NC}"

echo ""
echo -e "${YELLOW}[7/10] Criando cache de configuração...${NC}"
php artisan config:cache
echo -e "${GREEN}✓ Cache de configuração criado${NC}"

echo ""
echo -e "${YELLOW}[8/10] Criando cache de rotas...${NC}"
php artisan route:cache
echo -e "${GREEN}✓ Cache de rotas criado${NC}"

echo ""
echo -e "${YELLOW}[9/10] Criando cache de views...${NC}"
php artisan view:cache
echo -e "${GREEN}✓ Cache de views criado${NC}"

echo ""
echo -e "${YELLOW}[10/10] Otimizando autoloader...${NC}"
composer dump-autoload --optimize --no-dev
echo -e "${GREEN}✓ Autoloader otimizado${NC}"

echo ""
echo "=========================================="
echo -e "${GREEN}✓ Deploy concluído com sucesso!${NC}"
echo "=========================================="
echo ""
echo "Acesse: https://212lavanderia.com.br"
echo ""
echo "Credenciais padrão:"
echo "  Email: admin@lavanderia.com"
echo "  Senha: admin123"
echo ""
echo -e "${YELLOW}IMPORTANTE: Altere a senha após o primeiro login!${NC}"
echo ""
