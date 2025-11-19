<?php
require_once("helper/MyConexion.php");

class PartidaModel {
    private $conexion;

    public function __construct() {
        $db = new MyConexion("localhost", "root", "", "preguntas_respuestas");
        $this->conexion = $db->getConexion();
        $this->asegurarColumnasEstadisticas();
        $this->asegurarTablaPreguntasUsuarios();
    }

    public function getCategoriaIdPorNombre($nombre) {
        $nombreNormalizado = trim($nombre);
        $stmt = $this->conexion->prepare("SELECT id FROM categorias WHERE LOWER(nombre) = LOWER(?) LIMIT 1");
        $stmt->bind_param("s", $nombreNormalizado);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['id'] ?? null;
    }

    public function obtenerPreguntasPorCategoriaId($categoria_id, $usuarioId, $limite = 4) {
        $stmt = $this->conexion->prepare("
            SELECT p.* FROM preguntas p
            LEFT JOIN preguntas_usuarios pu ON pu.pregunta_id = p.id AND pu.usuario_id = ?
            WHERE p.categoria_id = ? AND pu.pregunta_id IS NULL AND p.activa = 1
            ORDER BY RAND()
            LIMIT ?
        ");
        $stmt->bind_param("iii", $usuarioId, $categoria_id, $limite);
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
        if (!$this->existeUsuario($usuarioId)) {
            throw new Exception("El usuario $usuarioId no existe en la tabla usuarios.");
        }
        if (!$this->existeCategoria($categoria_id)) {
            throw new Exception("La categoría $categoria_id no existe en la tabla categorias.");
        }

        $stmt = $this->conexion->prepare("
        INSERT INTO partidas (usuario_id, categoria_id, puntaje) 
        VALUES (?, ?, 0)");
        $stmt->bind_param("ii", $usuarioId, $categoria_id);
        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            error_log("Error insertando partida: " . $e->getMessage());
            throw new Exception("No se pudo crear la partida (FK). Verifica que el usuario y la categoría existan.");
        }
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

        return $partidas;
    }
    
    public function registrarEntregaPregunta($preguntaId) {
        $stmt = $this->conexion->prepare("UPDATE preguntas SET veces_entregada = veces_entregada + 1 WHERE id = ?");
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $this->recalcularDificultad($preguntaId);
    }
    
    public function registrarIncorrectaPregunta($preguntaId) {
        $stmt = $this->conexion->prepare("UPDATE preguntas SET veces_incorrecta = veces_incorrecta + 1 WHERE id = ?");
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $this->recalcularDificultad($preguntaId);
    }

    public function registrarPreguntaUsuario($usuarioId, $preguntaId, $correcta) {
        $stmt = $this->conexion->prepare("INSERT IGNORE INTO preguntas_usuarios (usuario_id, pregunta_id, correcta) VALUES (?,?,?)");
        $flag = $correcta ? 1 : 0;
        $stmt->bind_param("iii", $usuarioId, $preguntaId, $flag);
        $stmt->execute();
    }
    
    private function recalcularDificultad($preguntaId) {
        $stmt = $this->conexion->prepare("SELECT veces_entregada, veces_incorrecta FROM preguntas WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        if (!$data) return;
        
        $entregadas = (int)$data['veces_entregada'];
        $incorrectas = (int)$data['veces_incorrecta'];
        if ($entregadas <= 0) return;
        $aciertos = max($entregadas - $incorrectas, 0);
        $porcentaje = ($aciertos / $entregadas) * 100.0;
        
        if ($porcentaje >= 70) {
            $nivel = 'Fácil';
        } elseif ($porcentaje >= 40) {
            $nivel = 'Medio';
        } else {
            $nivel = 'Difícil';
        }
        
        $stmtUpd = $this->conexion->prepare("UPDATE preguntas SET porcentaje_acierto = ?, nivel_dificultad = ? WHERE id = ?");
        $stmtUpd->bind_param("dsi", $porcentaje, $nivel, $preguntaId);
        $stmtUpd->execute();
    }

    private function asegurarColumnasEstadisticas() {
        $dbNameResult = $this->conexion->query("SELECT DATABASE() AS db");
        $dbRow = $dbNameResult ? $dbNameResult->fetch_assoc() : null;
        $dbName = $dbRow['db'] ?? 'preguntas_respuestas';

        $colsNecesarias = [
            'veces_entregada' => "ALTER TABLE preguntas ADD COLUMN veces_entregada INT DEFAULT 0",
            'veces_incorrecta' => "ALTER TABLE preguntas ADD COLUMN veces_incorrecta INT DEFAULT 0",
            'porcentaje_acierto' => "ALTER TABLE preguntas ADD COLUMN porcentaje_acierto DECIMAL(5,2) DEFAULT 0.00",
            'nivel_dificultad' => "ALTER TABLE preguntas ADD COLUMN nivel_dificultad ENUM('Fácil','Medio','Difícil') DEFAULT 'Medio'"
        ];

        $checkStmt = $this->conexion->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'preguntas' AND COLUMN_NAME = ? LIMIT 1");
        foreach ($colsNecesarias as $col => $ddl) {
            $checkStmt->bind_param("ss", $dbName, $col);
            if ($checkStmt->execute()) {
                $res = $checkStmt->get_result();
                if ($res->num_rows === 0) {
                    $this->conexion->query($ddl);
                }
            }
        }
        $checkStmt->close();
    }

    private function existeUsuario($id) {
        $stmt = $this->conexion->prepare("SELECT 1 FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->num_rows === 1;
    }

    private function existeCategoria($id) {
        $stmt = $this->conexion->prepare("SELECT 1 FROM categorias WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->num_rows === 1;
    }

    private function asegurarTablaPreguntasUsuarios() {
        $res = $this->conexion->query("SHOW TABLES LIKE 'preguntas_usuarios'");
        if ($res && $res->num_rows === 0) {
            $ddl = "CREATE TABLE preguntas_usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                pregunta_id INT NOT NULL,
                correcta TINYINT(1) NOT NULL,
                fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_usuario_pregunta (usuario_id, pregunta_id),
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                FOREIGN KEY (pregunta_id) REFERENCES preguntas(id) ON DELETE CASCADE,
                INDEX idx_usuario (usuario_id),
                INDEX idx_pregunta (pregunta_id),
                INDEX idx_usuario_correcta (usuario_id, correcta)
            )";
            $this->conexion->query($ddl);
        }
    }
}