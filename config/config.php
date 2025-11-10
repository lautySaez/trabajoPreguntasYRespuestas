<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define("BASE_PATH", dirname(__DIR__) . "/");
define("URL_BASE", "/trabajoPreguntasYRespuestas/");

$config = parse_ini_file(BASE_PATH . "config/config.ini");

if (isset($config["timezone"])) {
    date_default_timezone_set($config["timezone"]);
}

define("DB_SERVER", $config["server"]);
define("DB_USER", $config["user"]);
define("DB_PASS", $config["pass"]);
define("DB_NAME", $config["database"]);


