<?php

require_once("helper/MyConexion.php");

class Reporte
{
    private $conn;

    public function __construct()
    {
        $conexion = new MyConexion("localhost", "root", "", "preguntas_respuestas");
        $this->conn = $conexion->getConexion();
    }

    public function obtenerReportes()
    {
        $sql = "
            SELECT 
                r.id,
                p.pregunta AS texto,
                u.nombre_usuario AS usuario,
                r.motivo,
                r.fecha_reporte,
                r.revisado
            FROM reportes r
            JOIN preguntas p ON r.id_pregunta = p.id
            JOIN usuarios u ON r.id_usuario = u.id
            ORDER BY r.fecha_reporte DESC
        ";

        $resultado = $this->conn->query($sql);

        if (!$resultado) {
            die("Error al obtener reportes: " . $this->conn->error);
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
