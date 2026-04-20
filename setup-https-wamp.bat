@echo off
echo ========================================
echo   Configuracao HTTPS para WAMP - Lavanderia
echo ========================================
echo.

echo Este script ira ajudar a configurar HTTPS no WAMP para permitir o uso da camera.
echo.

echo IMPORTANTE: Execute este script como Administrador!
echo.

pause

echo.
echo 1. Verificando se o WAMP esta instalado...

if not exist "C:\wamp64" (
    if not exist "C:\wamp" (
        echo ERRO: WAMP nao encontrado em C:\wamp64 ou C:\wamp
        echo Por favor, ajuste o caminho no script se necessario.
        pause
        exit /b 1
    ) else (
        set WAMP_PATH=C:\wamp
    )
) else (
    set WAMP_PATH=C:\wamp64
)

echo WAMP encontrado em: %WAMP_PATH%
echo.

echo 2. Parando servicos do WAMP...
net stop wampapache64 2>nul
net stop wampapache 2>nul
echo.

echo 3. Configurando SSL no Apache...

echo Criando certificado SSL auto-assinado...
echo.

set APACHE_PATH=%WAMP_PATH%\bin\apache\apache2.4.54
if not exist "%APACHE_PATH%" (
    for /d %%i in ("%WAMP_PATH%\bin\apache\apache*") do set APACHE_PATH=%%i
)

echo Usando Apache em: %APACHE_PATH%
echo.

echo 4. Criando diretorio para certificados...
if not exist "%APACHE_PATH%\conf\ssl" mkdir "%APACHE_PATH%\conf\ssl"

echo.
echo 5. Gerando certificado SSL...
echo.

cd /d "%APACHE_PATH%\bin"

echo Criando chave privada...
openssl genrsa -out "%APACHE_PATH%\conf\ssl\localhost.key" 2048

echo.
echo Criando certificado...
openssl req -new -x509 -key "%APACHE_PATH%\conf\ssl\localhost.key" -out "%APACHE_PATH%\conf\ssl\localhost.crt" -days 365 -subj "/C=BR/ST=SP/L=SaoPaulo/O=Lavanderia/OU=IT/CN=localhost"

echo.
echo 6. Configurando httpd.conf...

set HTTPD_CONF=%APACHE_PATH%\conf\httpd.conf

echo Fazendo backup do httpd.conf...
copy "%HTTPD_CONF%" "%HTTPD_CONF%.backup"

echo Habilitando modulos SSL...
powershell -Command "(gc '%HTTPD_CONF%') -replace '#LoadModule rewrite_module', 'LoadModule rewrite_module' | Out-File -encoding ASCII '%HTTPD_CONF%'"
powershell -Command "(gc '%HTTPD_CONF%') -replace '#LoadModule ssl_module', 'LoadModule ssl_module' | Out-File -encoding ASCII '%HTTPD_CONF%'"
powershell -Command "(gc '%HTTPD_CONF%') -replace '#Include conf/extra/httpd-ssl.conf', 'Include conf/extra/httpd-ssl.conf' | Out-File -encoding ASCII '%HTTPD_CONF%'"

echo.
echo 7. Configurando httpd-ssl.conf...

set SSL_CONF=%APACHE_PATH%\conf\extra\httpd-ssl.conf

echo Fazendo backup do httpd-ssl.conf...
copy "%SSL_CONF%" "%SSL_CONF%.backup"

echo Atualizando configuracao SSL...
powershell -Command "(gc '%SSL_CONF%') -replace 'ServerName www.example.com:443', 'ServerName localhost:443' | Out-File -encoding ASCII '%SSL_CONF%'"
powershell -Command "(gc '%SSL_CONF%') -replace 'DocumentRoot.*', 'DocumentRoot \"c:/wamp64/www\"' | Out-File -encoding ASCII '%SSL_CONF%'"
powershell -Command "(gc '%SSL_CONF%') -replace 'SSLCertificateFile.*', 'SSLCertificateFile \"conf/ssl/localhost.crt\"' | Out-File -encoding ASCII '%SSL_CONF%'"
powershell -Command "(gc '%SSL_CONF%') -replace 'SSLCertificateKeyFile.*', 'SSLCertificateKeyFile \"conf/ssl/localhost.key\"' | Out-File -encoding ASCII '%SSL_CONF%'"

echo.
echo 8. Reiniciando servicos do WAMP...
net start wampapache64 2>nul
if errorlevel 1 net start wampapache

echo.
echo ========================================
echo   CONFIGURACAO CONCLUIDA!
echo ========================================
echo.
echo Agora voce pode acessar:
echo.
echo HTTP:  http://localhost/lavanderia/public/motorista/dashboard
echo HTTPS: https://localhost/lavanderia/public/motorista/dashboard
echo.
echo IMPORTANTE:
echo - O navegador ira mostrar um aviso de seguranca (certificado auto-assinado)
echo - Clique em "Avancado" e "Prosseguir para localhost"
echo - Isso e normal para certificados de desenvolvimento
echo.
echo Para testar a camera, acesse:
echo https://localhost/lavanderia/public/test-camera.html
echo.

pause
