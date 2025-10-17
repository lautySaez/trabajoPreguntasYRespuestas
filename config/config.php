<?php
// Iniciamos sesión si aún no existe
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definimos rutas base del proyecto
define("BASE_PATH", dirname(__DIR__) . "/");
define("URL_BASE", "/trabajoPreguntasYRespuestas/");

// Cargamos el archivo .ini con la configuración
$config = parse_ini_file(BASE_PATH . "config/config.ini");

// Configuramos la zona horaria
if (isset($config["timezone"])) {
    date_default_timezone_set($config["timezone"]);
}

// Configuración de base de datos (opcional, si la usás acá)
define("DB_SERVER", $config["server"]);
define("DB_USER", $config["user"]);
define("DB_PASS", $config["pass"]);
define("DB_NAME", $config["database"]);


