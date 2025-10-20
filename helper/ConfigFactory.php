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

        $this->conexion = new MyConexion(
            $this->config["server"],
            $this->config["user"],
            $this->config["pass"],
            $this->config["database"]
        );

        $usuarioModel = new Usuario($this->conexion->getConexion());

        $this->objetos["LoginController"] = new LoginController($usuarioModel);

        $this->objetos["router"] = new NewRouter($this, "LoginController", "inicioSesion");

    }

    public function get($objectName)
    {
        return $this->objetos[$objectName] ?? null;
    }
}