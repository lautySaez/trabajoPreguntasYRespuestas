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

    public function obtenerReportes() {
        $sql = "
            SELECT 
                r.id,
                p.pregunta AS pregunta,
                u.nombre_usuario AS usuario,
                r.motivo,
                r.fecha_reporte,
                r.revisado,
                CASE 
                    WHEN r.revisado = 1 THEN 'Revisado'
                    ELSE 'Pendiente'
                END AS estado
            FROM reportes r
            LEFT JOIN preguntas p ON r.id_pregunta = p.id
            LEFT JOIN usuarios u ON r.id_usuario = u.id
            ORDER BY r.fecha_reporte DESC
        ";

        $resultado = $this->conn->query($sql);

        if (!$resultado) {
            die("Error al obtener reportes: " . $this->conn->error);
        }

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function crearReporte($id_pregunta, $id_usuario, $motivo) {
        $stmt = $this->conn->prepare("
            INSERT INTO reportes (id_pregunta, id_usuario, motivo, fecha_reporte, revisado)
            VALUES (?, ?, ?, NOW(), 0)
        ");

        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->conn->error);
        }

        $stmt->bind_param("iis", $id_pregunta, $id_usuario, $motivo);

        if (!$stmt->execute()) {
            die("Error al ejecutar la inserción: " . $stmt->error);
        }

        $stmt->close();
    }
}