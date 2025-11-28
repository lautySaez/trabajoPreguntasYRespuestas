<?php
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once "helper/ConfigFactory.php";

$configFactory = new ConfigFactory();
$router = new NewRouter($configFactory, "LoginController", "inicioSesion");

$controller = $_GET["controller"] ?? "LoginController";
$method = $_GET["method"] ?? "inicioSesion";

// For pretty route POST /login (no explicit method segment) invoke login() instead of inicioSesion
if ($_SERVER['REQUEST_METHOD'] === 'POST'
	&& (!isset($_GET['method']) || empty($_GET['method']))
	&& (strtolower($controller) === 'login' || strtolower($controller) === 'logincontroller')) {
	$method = 'login';
}

$router->executeController($controller, $method);
