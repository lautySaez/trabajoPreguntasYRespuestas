@echo off
echo ======================================
echo  CONFIGURACION DE PHPMAILER
echo ======================================
echo.

echo Verificando si Composer esta instalado...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Composer no esta instalado o no esta en el PATH
    echo.
    echo Por favor instala Composer desde: https://getcomposer.org/download/
    echo.
    pause
    exit /b 1
) else (
    echo Composer encontrado
)
echo.

echo Instalando dependencias de PHPMailer...
composer install --no-dev

if %errorlevel% neq 0 (
    echo.
    echo Error al instalar dependencias con Composer
    echo Intentando método alternativo...
    
    echo Creando directorio vendor si no existe...
    if not exist "vendor" mkdir vendor
    if not exist "vendor\phpmailer" mkdir vendor\phpmailer
    
    echo Descargando PHPMailer...
    powershell -Command "& {Invoke-WebRequest -Uri 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip' -OutFile 'phpmailer.zip'}"
    
    if exist "phpmailer.zip" (
        echo Extrayendo PHPMailer...
        powershell -Command "& {Expand-Archive -Path 'phpmailer.zip' -DestinationPath 'temp' -Force}"
        
        if exist "temp\PHPMailer-6.9.1" (
            echo Copiando archivos...
            xcopy "temp\PHPMailer-6.9.1" "vendor\phpmailer\phpmailer" /E /I /Y >nul
            
            echo Limpiando archivos temporales...
            rmdir /s /q temp >nul 2>&1
            del phpmailer.zip >nul 2>&1

            echo PHPMailer instalado manualmente
        ) else (
            echo Error al extraer PHPMailer
        )
    ) else (
        echo Error al descargar PHPMailer
    )
) else (
    echo Dependencias instaladas correctamente con Composer
)

echo.
echo Verificando instalación...
php test_phpmailer.php

echo.
echo ======================================
echo  CONFIGURACION COMPLETADA
echo ======================================
echo.
echo Si ves "PHPMailer configurado correctamente" arriba,
echo entonces la instalacion fue exitosa.
echo.
pause