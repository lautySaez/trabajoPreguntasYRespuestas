<?php
class EditorModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function obtenerCategorias() {
        $stmt = $this->conexion->prepare("SELECT * FROM categorias");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPreguntasPorCategoria($categoria_id = null) {
        if ($categoria_id) {
            $stmt = $this->conexion->prepare("SELECT * FROM preguntas WHERE categoria_id = ?");
            $stmt->bind_param("i", $categoria_id);
        } else {
            $stmt = $this->conexion->prepare("SELECT * FROM preguntas");
        }
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($resultado as &$pregunta) {
            $pregunta["respuestas"] = [
                ["id" => 1, "texto" => $pregunta["respuesta_1"]],
                ["id" => 2, "texto" => $pregunta["respuesta_2"]],
                ["id" => 3, "texto" => $pregunta["respuesta_3"]],
                ["id" => 4, "texto" => $pregunta["respuesta_4"]],
            ];
        }

        return $resultado;
    }

    public function obtenerPreguntaPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM preguntas WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $pregunta = $stmt->get_result()->fetch_assoc();

        if ($pregunta) {
            $pregunta["respuestas"] = [
                ["id" => 1, "texto" => $pregunta["respuesta_1"]],
                ["id" => 2, "texto" => $pregunta["respuesta_2"]],
                ["id" => 3, "texto" => $pregunta["respuesta_3"]],
                ["id" => 4, "texto" => $pregunta["respuesta_4"]],
            ];
        }

        return $pregunta;
    }

    public function crearPregunta($categoria_id, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $respuesta_correcta) {
        $stmt = $this->conexion->prepare("
            INSERT INTO preguntas (categoria_id, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, respuesta_correcta)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssi", $categoria_id, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $respuesta_correcta);
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    public function editarPregunta($id, $categoria_id, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $respuesta_correcta) {
        $stmt = $this->conexion->prepare("
            UPDATE preguntas
            SET categoria_id = ?, pregunta = ?, respuesta_1 = ?, respuesta_2 = ?, respuesta_3 = ?, respuesta_4 = ?, respuesta_correcta = ?
            WHERE id = ?
        ");
        $stmt->bind_param("isssssii", $categoria_id, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $respuesta_correcta, $id);
        $stmt->execute();
    }

    public function borrarPregunta($id) {
        $stmt = $this->conexion->prepare("DELETE FROM preguntas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function registrarInforme($pregunta_id, $tipo_accion, $motivo, $pregunta_data = null) {

        $stmt = $this->conexion->prepare("
        INSERT INTO informepreguntas 
        (pregunta_id, tipo_accion, motivo, pregunta, r1, r2, r3, r4, correcta, categoria_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

        $stmt->bind_param(
            "issssssiii",
            $pregunta_id,
            $tipo_accion,
            $motivo,
            $pregunta_data['pregunta'],
            $pregunta_data['r1'],
            $pregunta_data['r2'],
            $pregunta_data['r3'],
            $pregunta_data['r4'],
            $pregunta_data['correcta'],
            $pregunta_data['categoria_id']
        );

        $stmt->execute();
    }

}

