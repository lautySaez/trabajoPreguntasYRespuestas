<?php

class Ranking {

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Obtiene top N jugadores
    public function obtenerTopJugadores($limite = 3)
    {
        $sql = "
            SELECT 
                u.nombre_usuario,
                u.foto_perfil,
                SUM(p.puntaje) AS puntaje_total
            FROM partidas p
            JOIN usuarios u ON u.id = p.usuario_id
            GROUP BY p.usuario_id
            ORDER BY puntaje_total DESC
            LIMIT ?
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}