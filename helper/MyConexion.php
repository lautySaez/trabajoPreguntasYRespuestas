<?php

class MyConexion
{
    private $conexion;

    
    public function __construct($server, $user, $pass, $database)
    {
        $config = parse_ini_file("config/config.ini");
        $this->conexion = new mysqli($config['server'], $config['user'], $config['pass'], $config['database']);
        if ($this->conexion->connect_error) {
            die("Error en la conexión: " . $this->conexion->connect_error);
        }
    }

    public function getConexion()
    {
        return $this->conexion;
    }
}