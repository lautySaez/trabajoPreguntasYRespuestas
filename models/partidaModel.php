<?php
require_once("helper/MyConexion.php");

class PartidaModel {
    private $conexion;

    public function __construct() {
        // CORRECCIÓN: pasar los 4 parámetros obligatorios
        $db = new MyConexion("localhost", "root", "", "preguntas_respuestas");
        $this->conexion = $db->getConexion();
    }

    public function getCategoriaIdPorNombre($nombre) {
    $stmt = $this->conexion->prepare("SELECT id FROM categorias WHERE nombre = ? LIMIT 1");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado['id'] ?? null;
    }

    public function obtenerPreguntasPorCategoriaId($categoria_id, $limite = 4) {
        $stmt = $this->conexion->prepare("
            SELECT * FROM preguntas
            WHERE categoria_id = ?
            ORDER BY RAND()
            LIMIT ?
        ");
        $stmt->bind_param("ii", $categoria_id, $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $preguntas = [];
        while ($pregunta = $resultado->fetch_assoc()) {
            $pregunta["respuestas"] = [
                ["id" => 1, "texto" => $pregunta["respuesta_1"]],
                ["id" => 2, "texto" => $pregunta["respuesta_2"]],
                ["id" => 3, "texto" => $pregunta["respuesta_3"]],
                ["id" => 4, "texto" => $pregunta["respuesta_4"]]
            ];
            $preguntas[] = $pregunta;
        }

        return $preguntas;
    }

    public function registrarPartida($usuarioId, $categoria_id) {
        $stmt = $this->conexion->prepare("
        INSERT INTO partidas (usuario_id, categoria_id, puntaje) 
        VALUES (?, ?, 0)");
        $stmt->bind_param("ii", $usuarioId, $categoria_id); // <-- ambos enteros
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    public function actualizarPuntaje($partidaId, $puntaje) {
        $stmt = $this->conexion->prepare("
            UPDATE partidas SET puntaje = ? WHERE id = ?
        ");
        $stmt->bind_param("ii", $puntaje, $partidaId);
        $stmt->execute();
    }

    public function getUltimasPartidas($usuarioId, $limite = 5) {
        $stmt = $this->conexion->prepare("
            SELECT fecha_inicio, puntaje 
            FROM partidas 
            WHERE usuario_id = ? 
            ORDER BY fecha_inicio DESC 
            LIMIT ?
        ");
        $stmt->bind_param("ii", $usuarioId, $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $partidas = [];
        while ($row = $resultado->fetch_assoc()) {
            $partidas[] = $row;
        }

        return $partidas; // devuelve array vacío si no hay resultados
    }
}