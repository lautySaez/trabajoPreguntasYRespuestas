<?php

class Ranking {

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerTopJugadores($limite = 5)
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
    } // Calcula y devuelve la clasificación de los jugadores.
    // La consulta suma el puntaje de todas las partidas jugadas por cada usuario (GROUP BY p.usuario_id).
    // Luego, ordena el resultado por puntaje_total de forma descendente
    // y limita la cantidad de resultados según el valor de $limite (por defecto,
    // muestra los 5 mejores jugadores).
    // Devuelve el nombre_usuario, foto_perfil y el puntaje_total de cada jugador del ranking.
}