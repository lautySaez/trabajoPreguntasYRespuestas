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

    public function obtenerCategoriaPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM categorias WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crearCategoria($nombre, $color = '#FFFFFF', $icono = '❓', $descripcion = '', $activa = 1) {
        $stmt = $this->conexion->prepare("
            INSERT INTO categorias (nombre, color, icono, descripcion, activa)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $nombre, $color, $icono, $descripcion, $activa);
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    public function borrarCategoria($id) {
        $stmt = $this->conexion->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function contarPreguntasPorCategoria($categoria_id) {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM preguntas WHERE categoria_id = ?");
        $stmt->bind_param("i", $categoria_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['total'] ?? 0;
    }

    public function obtenerPreguntasPorCategoria($categoria_id = null, $solo_reportadas = null) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, COUNT(r.id) as reportes_count_activos
                FROM preguntas p 
                INNER JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN reportes r ON p.id = r.pregunta_id AND r.estado = 'Activo'";

        $params = [];
        $tipos = "";
        $whereAdded = false;
        if ($solo_reportadas !== null) {
            $sql .= " GROUP BY p.id";
            $whereAdded = true;

            if ($solo_reportadas === true) {
                $sql .= " HAVING reportes_count_activos > 0";
            } elseif ($solo_reportadas === false) {
                $sql .= " HAVING reportes_count_activos = 0";
            }
        }

        if ($solo_reportadas === null) {
            $sql .= " GROUP BY p.id";
        }

        if ($categoria_id) {

            if ($solo_reportadas !== null) {

                $where_clause = " WHERE p.categoria_id = ?";

                if (!$whereAdded) {
                    $sql .= " WHERE p.categoria_id = ?";
                    $whereAdded = true;
                } else {

                }

                $tipos .= "i";
                $params[] = &$categoria_id;

                if ($solo_reportadas === null) {
                    $sql = str_replace("GROUP BY p.id", "WHERE p.categoria_id = ? GROUP BY p.id", $sql);
                    array_pop($params);
                    $params = [&$categoria_id];
                    $tipos = "i";
                }
            } else {
                $sql .= " WHERE p.categoria_id = ?";
                $tipos .= "i";
                $params[] = &$categoria_id;
            }
        }

        $sql = "SELECT p.*, c.nombre as categoria_nombre, COUNT(r.id) as reportes_count_activos
                FROM preguntas p 
                INNER JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN reportes r ON p.id = r.pregunta_id AND r.estado = 'Activo'
                " . ($categoria_id ? "WHERE p.categoria_id = ?" : "") . "
                GROUP BY p.id
                " . (($solo_reportadas === true) ? "HAVING reportes_count_activos > 0" : "") . "
                " . (($solo_reportadas === false) ? "HAVING reportes_count_activos = 0" : "");

        $stmt = $this->conexion->prepare($sql);

        if ($categoria_id) {
            $stmt->bind_param("i", $categoria_id);
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
            $pregunta['reportes_count'] = $pregunta['reportes_count_activos'];
        }

        return $resultado;
    }

    public function obtenerPreguntaPorId($id) {
        $stmt = $this->conexion->prepare("SELECT p.*, c.nombre as categoria_nombre FROM preguntas p
                                         INNER JOIN categorias c ON p.categoria_id = c.id
                                         WHERE p.id = ? LIMIT 1");
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
        $editor_id = $_SESSION['usuario']['id'] ?? null;

        $stmt = $this->conexion->prepare("
        INSERT INTO informepreguntas 
        (pregunta_id, editor_id, tipo_accion, motivo, pregunta, r1, r2, r3, r4, correcta, categoria_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iisssssssii",
            $pregunta_id,
            $editor_id,
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

    public function crearPregunta($categoria_id, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $respuesta_correcta) {
        $stmt = $this->conexion->prepare("
        INSERT INTO preguntas (categoria_id, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, respuesta_correcta)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssi", $categoria_id, $pregunta, $respuesta_1, $respuesta_2, $respuesta_3, $respuesta_4, $respuesta_correcta);
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    public function contarReportesPorPregunta($pregunta_id) {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) AS total FROM reportes WHERE pregunta_id = ? AND estado = 'Activo'");
        $stmt->bind_param("i", $pregunta_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['total'] ?? 0;
    }

    public function obtenerReportesAgrupados($estado = 'Activo') {
        $sql = "
            SELECT 
                p.id AS pregunta_id, 
                p.pregunta AS texto_pregunta,
                c.nombre AS categoria_nombre,
                COUNT(r.id) AS total_reportes,
                MAX(r.fecha_reporte) AS ultimo_reporte
            FROM reportes r
            INNER JOIN preguntas p ON r.pregunta_id = p.id
            INNER JOIN categorias c ON p.categoria_id = c.id
            WHERE r.estado = ?
            GROUP BY p.id, p.pregunta, c.nombre
            ORDER BY total_reportes DESC, ultimo_reporte DESC
        ";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $estado);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerReportesDetalladosPorPregunta($pregunta_id) {
        $sql = "
            SELECT 
                r.motivo, 
                r.fecha_reporte, 
                r.estado,
                u.nombre_usuario AS nombre_usuario
            FROM reportes r
            INNER JOIN usuarios u ON r.usuario_id = u.id
            WHERE r.pregunta_id = ?
            ORDER BY r.fecha_reporte DESC
        ";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $pregunta_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function marcarReporteComoResuelto($pregunta_id, $motivo_resolucion = null) {
        $stmt = $this->conexion->prepare("
            UPDATE reportes 
            SET estado = 'Resuelto' 
            WHERE pregunta_id = ? AND estado = 'Activo'
        ");
        $stmt->bind_param("i", $pregunta_id);
        $stmt->execute();

        if ($motivo_resolucion) {
            $this->registrarInforme(
                $pregunta_id,
                'Reporte Resuelto',
                $motivo_resolucion,
                ['pregunta' => '', 'r1' => '', 'r2' => '', 'r3' => '', 'r4' => '', 'correcta' => 0, 'categoria_id' => 0]
            );
        }
    }
}