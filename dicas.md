SUBIR
git status
git add .
git commit -m "Sua mensagem"
git push origin main

-----------------------------



cd /home/lavanderia/htdocs/212lavanderia.com.br
git pull origin main

php artisan migrate
php artisan db:seed --class=NiveisAcessoSeeder

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
git pull origin main

php artisan migrate
php artisan db:seed --class=NiveisAcessoSeeder

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache

logs
# Ver logs do Laravel em tempo real
tail -f storage/logs/laravel.log

# Ver apenas as últimas 50 linhas e continuar monitorando
tail -n 50 -f storage/logs/laravel.log

# Ver logs do servidor web (nginx/apache)
tail -f /var/log/nginx/error.log
# ou
tail -f /var/log/apache2/error.log


----------------------------------------

# Ver últimas 100 linhas do log
tail -n 100 storage/logs/laravel.log

# Filtrar apenas erros
grep "ERROR" storage/logs/laravel.log

# Ver logs de hoje
grep "$(date +%Y-%m-%d)" storage/logs/laravel.log
