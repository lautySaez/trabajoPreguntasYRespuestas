<?php

class RankingModel {

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerRanking()
    {
        $sql = "SELECT 
                    u.nombre_usuario,
                    u.foto_perfil,
                    r.puntaje_total
                FROM ranking_jugadores r
                JOIN usuarios u ON u.id = r.usuario_id
                ORDER BY r.puntaje_total DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
}
