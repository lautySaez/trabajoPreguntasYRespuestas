<?php

class MyConexion
{
    private $conexion;

    public function __construct($server, $user, $pass, $database)
    {
        $this->conexion = new mysqli("localhost", "root", "", "preguntas_respuestas");
        if ($this->conexion->connect_error) {
            die("Error en la conexión: " . $this->conexion->connect_error);
        }
    }

    public function getConexion()
    {
        return $this->conexion;
    }
}