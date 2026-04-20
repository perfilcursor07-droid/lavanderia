# Como Ativar a Página de Suspensão no Servidor

## Passo a Passo

### 1. Conectar ao servidor via SSH
```bash
ssh root@212lavanderia.com.br
```

### 2. Ir para o diretório do projeto
```bash
cd /home/lavanderia/htdocs/212lavanderia.com.br/public/lavanderia
```

### 3. Configurar permissões do Git (se necessário)
```bash
git config --global --add safe.directory /home/lavanderia/htdocs/212lavanderia.com.br/public/lavanderia
```

### 4. Puxar as atualizações do Git
```bash
git pull origin main
```

### 5. Verificar se os arquivos foram baixados
```bash
ls -la public/suspended.html
ls -la public/index_suspended.html
ls -la public/.htaccess
```

### 6. Limpar cache do Laravel
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 7. Testar o redirecionamento
Acesse no navegador: https://212lavanderia.com.br

Você deve ser redirecionado automaticamente para: https://212lavanderia.com.br/suspended.html

---

## ✅ Pronto! O site está suspenso

Qualquer acesso ao site será redirecionado para a página de suspensão AWS-style.

---

## Como DESATIVAR a suspensão depois

### Opção 1: Comentar as linhas no .htaccess
```bash
nano public/.htaccess
```

Comente estas linhas (adicione # no início):
```apache
# RewriteCond %{REQUEST_URI} !^/suspended\.html$
# RewriteCond %{REQUEST_URI} !^/suspended$
# RewriteRule ^(.*)$ /suspended.html [L,R=302]
```

Salve (Ctrl+O) e saia (Ctrl+X)

### Opção 2: Restaurar backup do .htaccess
```bash
cp public/.htaccess.backup public/.htaccess
```

### Limpar cache novamente
```bash
php artisan config:clear
php artisan cache:clear
```

---

## Comandos Rápidos (Copiar e Colar)

### Ativar suspensão:
```bash
cd /home/lavanderia/htdocs/212lavanderia.com.br/public/lavanderia
git pull origin main
php artisan config:clear
php artisan cache:clear
```

### Desativar suspensão:
```bash
cd /home/lavanderia/htdocs/212lavanderia.com.br/public/lavanderia
nano public/.htaccess
# Comente as 3 linhas do SUSPEND SITE
php artisan config:clear
php artisan cache:clear
```
