<?php

class Ranking {
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerTopJugadores($limite = 5) {
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

    public function obtenerMejoresPartidas($limite = 5) {
        $sql = "
            SELECT 
                u.nombre_usuario,
                u.foto_perfil,
                MAX(p.puntaje) AS mejor_partida
            FROM partidas p
            JOIN usuarios u ON u.id = p.usuario_id
            GROUP BY p.usuario_id
            ORDER BY mejor_partida DESC
            LIMIT ?
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}