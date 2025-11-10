<?php
require_once __DIR__ . '/config/config.php';
require_once "helper/ConfigFactory.php";

$configFactory = new ConfigFactory();

$router = new NewRouter($configFactory, "LoginController", "inicioSesion");

$controller = $_GET["controller"] ?? "LoginController";
$method = $_GET["method"] ?? "inicioSesion";

$router->executeController($controller, $method);
