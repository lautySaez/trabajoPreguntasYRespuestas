<?php
// Archivo de prueba para verificar PHPMailer
echo "=== VERIFICACION DE PHPMAILER ===\n";

// Intentar cargar PHPMailer desde diferentes ubicaciones
$autoload_paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php'
];

$autoload_loaded = false;
foreach ($autoload_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoload_loaded = true;
        echo "✅ Autoload cargado desde: $path\n";
        break;
    }
}

if (!$autoload_loaded) {
    // Fallback: cargar PHPMailer directamente
    $direct_paths = [
        __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php',
        __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php',
        __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php'
    ];
    
    $all_exist = true;
    foreach ($direct_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            echo "✅ Cargado: " . basename($path) . "\n";
        } else {
            echo "❌ No encontrado: $path\n";
            $all_exist = false;
        }
    }
    
    if ($all_exist) {
        echo "✅ PHPMailer cargado directamente\n";
    } else {
        echo "❌ No se pudo cargar PHPMailer\n";
        exit(1);
    }
} 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    // Verificar si la clase existe
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "✅ Clase PHPMailer encontrada correctamente.\n";
        
        // Crear instancia
        $mail = new PHPMailer(false);
        echo "✅ Instancia de PHPMailer creada exitosamente.\n";
        
        // Verificar constantes importantes
        $constants = [
            'PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS',
            'PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS'
        ];
        
        foreach ($constants as $constant) {
            if (defined($constant)) {
                echo "✅ Constante disponible: $constant\n";
            } else {
                echo "❌ Constante no disponible: $constant\n";
            }
        }
        
        echo "\n🎉 PHPMailer configurado correctamente!\n";
        echo "✅ El proyecto debería funcionar sin problemas.\n";
        
    } else {
        echo "❌ Clase PHPMailer NO encontrada.\n";
        echo "❌ Ejecuta 'composer install' o 'instalar_phpmailer.bat'\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error al probar PHPMailer: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA VERIFICACION ===\n";
?>