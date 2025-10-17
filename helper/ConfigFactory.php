<?php
include_once("helper/MyConexion.php");
include_once("helper/IncludeFileRenderer.php");
include_once("helper/NewRouter.php");
include_once("controllers/LoginController.php");
include_once("models/usuario.php");

class ConfigFactory
{
    private $config;
    private $objetos;
    private $conexion;

    public function __construct()
    {
        $this->config = parse_ini_file("config/config.ini");

        // Crear conexión
        $this->conexion = new MyConexion(
            $this->config["server"],
            $this->config["user"],
            $this->config["pass"],
            $this->config["database"]
        );

        // Modelos
        $usuarioModel = new Usuario($this->conexion->getConexion());

        // Controladores
        $this->objetos["LoginController"] = new LoginController($usuarioModel);

        // Router
        $this->objetos["router"] = new NewRouter($this, "LoginController", "inicioSesion");

    }

    public function get($objectName)
    {
        return $this->objetos[$objectName] ?? null;
    }
}