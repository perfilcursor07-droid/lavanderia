# Deploy Rápido - CloudPanel

## Credenciais do Servidor
- **SSH**: `ssh lavanderia@104.248.185.39`
- **Banco**: lavanderia / @@2025@@Ekb

## Comandos para executar no servidor

```bash
# 1. Conectar via SSH
ssh lavanderia@104.248.185.39

# 2. Navegar e clonar o projeto
cd /home/lavanderia/htdocs
rm -rf 212lavanderia.com.br
git clone https://github.com/erickafram/lavanderia.git 212lavanderia.com.br
cd 212lavanderia.com.br

# 3. Executar script de deploy automático
chmod +x deploy-cloudpanel.sh
./deploy-cloudpanel.sh
```

## OU fazer manualmente:

```bash
# Configurar .env
cp .env.production .env

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Permissões
chmod -R 755 storage bootstrap/cache

# Migrations e seeders
php artisan migrate --force
php artisan db:seed --force

# Otimizar
php artisan optimize
```

## Após o deploy

1. Acesse: https://212lavanderia.com.br
2. Login: admin@lavanderia.com / admin123
3. **ALTERE A SENHA IMEDIATAMENTE!**

## Configurar SSL no CloudPanel

1. CloudPanel > SSL/TLS
2. Let's Encrypt > Install

## Configurações PHP recomendadas

- PHP Version: 8.3
- memory_limit: 256 MB
- upload_max_filesize: 64 MB

## Troubleshooting

```bash
# Ver logs
tail -f storage/logs/laravel.log

# Limpar tudo
php artisan optimize:clear

# Recriar caches
php artisan optimize
```

## Atualizar código depois

```bash
cd /home/lavanderia/htdocs/212lavanderia.com.br
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```
