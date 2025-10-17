<?php
require_once __DIR__ . '/config/config.php';
require_once "helper/ConfigFactory.php";

$configFactory = new ConfigFactory();

// Creamos el router con valores por defecto del config.ini
$router = new NewRouter($configFactory, "LoginController", "inicioSesion");

// Capturamos los parámetros de la URL
$controller = $_GET["controller"] ?? "LoginController";
$method = $_GET["method"] ?? "inicioSesion";

// Ejecutamos el controlador y método
$router->executeController($controller, $method);

