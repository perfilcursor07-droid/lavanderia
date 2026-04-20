# Guia de Instalação - CloudPanel

## Informações do Servidor
- **Domínio**: 212lavanderia.com.br
- **IP**: 104.248.185.39
- **Usuário**: lavanderia
- **Diretório**: /home/lavanderia/htdocs/212lavanderia.com.br/public
- **PHP**: 8.2 ou superior (recomendado 8.3)

## Passo 1: Conectar via SSH

```bash
ssh lavanderia@104.248.185.39
```

## Passo 2: Navegar para o diretório correto

```bash
cd /home/lavanderia/htdocs/212lavanderia.com.br
```

**IMPORTANTE**: O diretório `public` já existe, mas você precisa clonar o projeto um nível acima!

## Passo 3: Remover o diretório public atual e clonar o projeto

```bash
# Voltar para o diretório pai
cd /home/lavanderia/htdocs

# Remover o diretório atual (se estiver vazio ou apenas com index.php)
rm -rf 212lavanderia.com.br

# Clonar o repositório
git clone https://github.com/erickafram/lavanderia.git 212lavanderia.com.br

# Entrar no diretório do projeto
cd 212lavanderia.com.br
```

## Passo 4: Instalar dependências do Composer

```bash
# Verificar se o Composer está instalado
composer --version

# Se não estiver instalado, instalar:
# curl -sS https://getcomposer.org/installer | php
# sudo mv composer.phar /usr/local/bin/composer

# Instalar dependências
composer install --no-dev --optimize-autoloader
```

## Passo 5: Configurar o arquivo .env

**OPÇÃO 1 - Usar o arquivo pronto (RECOMENDADO)**:
```bash
# Copiar o arquivo de produção já configurado
cp .env.production .env
```

**OPÇÃO 2 - Editar manualmente**:
```bash
# Copiar o arquivo de exemplo
cp .env.example .env

# Editar o arquivo .env
nano .env
```

### Configurações necessárias no .env:

```env
APP_NAME="Sistema de Gestão de Lavanderia"
APP_ENV=production
APP_KEY=base64:LurYSej4gqSKFyvzmmWxgm5zyMskjIpMnV27cDRqV+I=
APP_DEBUG=false
APP_URL=https://212lavanderia.com.br

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lavanderia
DB_USERNAME=lavanderia
DB_PASSWORD=@@2025@@Ekb

SESSION_DRIVER=database
SESSION_LIFETIME=120
```

**Salvar**: Ctrl+O, Enter, Ctrl+X

## Passo 6: Gerar chave da aplicação (OPCIONAL)

**NOTA**: O arquivo `.env.production` já tem uma chave gerada. Só execute este comando se quiser gerar uma nova chave:

```bash
php artisan key:generate
```

## Passo 7: Configurar permissões

```bash
# Dar permissões corretas aos diretórios
chmod -R 755 storage bootstrap/cache
chown -R lavanderia:lavanderia storage bootstrap/cache
```

## Passo 8: Banco de dados (já configurado)

**Informações do banco**:
- Database: `lavanderia`
- Username: `lavanderia`
- Password: `@@2025@@Ekb`

Essas credenciais já estão no arquivo `.env.production`

## Passo 9: Executar migrations e seeders

```bash
# Executar migrations
php artisan migrate --force

# Executar seeders para dados iniciais
php artisan db:seed --force
```

## Passo 10: Otimizar a aplicação

```bash
# Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Criar caches otimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Passo 11: Configurar SSL no CloudPanel

1. No CloudPanel, vá em **SSL/TLS**
2. Selecione **Let's Encrypt**
3. Clique em **Install**

## Passo 12: Ajustar configurações PHP no CloudPanel

Recomendações:
- **PHP Version**: 8.3
- **memory_limit**: 256 MB
- **max_execution_time**: 1m
- **max_input_time**: 1m
- **post_max_size**: 64 MB
- **upload_max_filesize**: 64 MB

## Passo 13: Criar usuário admin

```bash
php artisan db:seed --class=UsuarioAdminSeeder
```

**Credenciais padrão**:
- Email: admin@lavanderia.com
- Senha: admin123

**IMPORTANTE**: Altere a senha após o primeiro login!

## Verificação Final

Acesse: https://212lavanderia.com.br

Se tudo estiver correto, você verá a tela de login.

## Troubleshooting

### Erro 500
```bash
# Verificar logs
tail -f storage/logs/laravel.log
```

### Permissões
```bash
# Se houver problemas de permissão
sudo chown -R lavanderia:lavanderia /home/lavanderia/htdocs/212lavanderia.com.br
sudo chmod -R 755 /home/lavanderia/htdocs/212lavanderia.com.br
sudo chmod -R 775 /home/lavanderia/htdocs/212lavanderia.com.br/storage
sudo chmod -R 775 /home/lavanderia/htdocs/212lavanderia.com.br/bootstrap/cache
```

### Banco de dados não conecta
```bash
# Testar conexão
php artisan tinker
# Dentro do tinker:
DB::connection()->getPdo();
```

## Comandos Úteis

```bash
# Ver status da aplicação
php artisan about

# Limpar todos os caches
php artisan optimize:clear

# Recriar caches
php artisan optimize

# Ver rotas
php artisan route:list

# Atualizar código do Git
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

## Backup

```bash
# Backup do banco de dados
mysqldump -u lavanderia -p lavanderia > backup_$(date +%Y%m%d).sql

# Backup dos arquivos
tar -czf backup_files_$(date +%Y%m%d).tar.gz /home/lavanderia/htdocs/212lavanderia.com.br
```

## Suporte

Para problemas, verifique:
1. Logs do Laravel: `storage/logs/laravel.log`
2. Logs do servidor: CloudPanel > Logs
3. Permissões de arquivos
4. Configurações do .env
