<?php
require_once("helper/MyConexion.php");

class PartidaModel {
    private $conexion;

    public function __construct() {
        $db = new MyConexion("localhost", "root", "", "preguntas_respuestas");
        $this->conexion = $db->getConexion();
        $this->asegurarColumnasEstadisticas();
        $this->asegurarTablaPreguntasUsuarios();
        $this->asegurarTablaPreguntasTiempos();
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function getCategoriaIdPorNombre($nombre) {
        $nombreNormalizado = trim($nombre);
        $stmt = $this->conexion->prepare("SELECT id FROM categorias WHERE LOWER(nombre) = LOWER(?) LIMIT 1");
        $stmt->bind_param("s", $nombreNormalizado);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        return $resultado['id'] ?? null;
    } // Busca y devuelve el id de una categoría basándose en su nombre (ignorando mayúsculas y minúsculas).

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
    } // Recupera preguntas aleatorias para una categoría específica.
    // Excluye las preguntas que el usuario ya ha respondido (consultando preguntas_usuarios)
    // y asegura que la pregunta esté activa.

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
    } // Crea una nueva entrada en la tabla partidas con puntaje inicial de 0
    // y devuelve el ID de la nueva partida.

    public function actualizarPuntaje($partidaId, $puntaje) {
        $stmt = $this->conexion->prepare("
            UPDATE partidas SET puntaje = ? WHERE id = ?
        ");
        $stmt->bind_param("ii", $puntaje, $partidaId);
        $stmt->execute();
    } // Actualiza el puntaje final de una partida ya registrada.

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
    } // Obtiene el historial de las últimas partidas jugadas por un usuario.
    
    public function registrarEntregaPregunta($preguntaId) {
        $stmt = $this->conexion->prepare("UPDATE preguntas SET veces_entregada = veces_entregada + 1 WHERE id = ?");
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $this->recalcularDificultad($preguntaId);
    } // Incrementa el contador veces_entregada de la pregunta y llama a recalcularDificultad.
    
    public function registrarIncorrectaPregunta($preguntaId) {
        $stmt = $this->conexion->prepare("UPDATE preguntas SET veces_incorrecta = veces_incorrecta + 1 WHERE id = ?");
        $stmt->bind_param("i", $preguntaId);
        $stmt->execute();
        $this->recalcularDificultad($preguntaId);
    } // Incrementa el contador veces_incorrecta de la pregunta y llama a recalcularDificultad.

    public function registrarPreguntaUsuario($usuarioId, $preguntaId, $correcta) {
        $stmt = $this->conexion->prepare("INSERT IGNORE INTO preguntas_usuarios (usuario_id, pregunta_id, correcta) VALUES (?,?,?)");
        $flag = $correcta ? 1 : 0;
        $stmt->bind_param("iii", $usuarioId, $preguntaId, $flag);
        $stmt->execute();
    } // Registra la respuesta individual de un usuario a una pregunta en la tabla preguntas_usuarios.
    // Utiliza INSERT IGNORE para evitar duplicados.
    
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
    } // Calcula y actualiza la dificultad de la pregunta. Usa la fórmula:
    // $((\text{veces\_entregada} - \text{veces\_incorrecta}) / \text{veces\_entregada}) \times 100$.
    // Asigna nivel_dificultad como: 'Fácil' ($\ge 70\%$ acierto),
    // 'Medio' ($\ge 40\%$ acierto),
    // 'Difícil' (el resto).

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
    } // Audita y crea las columnas de estadísticas en la tabla preguntas
    // (veces_entregada, veces_incorrecta, porcentaje_acierto, nivel_dificultad)
    // si no existen, permitiendo que la lógica adaptativa funcione.

    private function existeUsuario($id) {
        $stmt = $this->conexion->prepare("SELECT 1 FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->num_rows === 1;
    } // Métodos de utilidad para verificar la existencia de un usuarioId o categoria_id
    // antes de crear una nueva partida (asegurando la integridad referencial).

    private function existeCategoria($id) {
        $stmt = $this->conexion->prepare("SELECT 1 FROM categorias WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->num_rows === 1;
    } // // Métodos de utilidad para verificar la existencia de un usuarioId o categoria_id
    // antes de crear una nueva partida (asegurando la integridad referencial).

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
    } // Crea la tabla preguntas_usuarios si no existe.
    // Esta tabla registra si un jugador específico ya ha respondido una pregunta y si lo hizo correctamente.

    private function asegurarTablaPreguntasTiempos() {
        $res = $this->conexion->query("SHOW TABLES LIKE 'preguntas_tiempos'");
        if ($res && $res->num_rows === 0) {
            $ddl = "CREATE TABLE preguntas_tiempos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                partida_id INT NOT NULL,
                usuario_id INT NOT NULL,
                pregunta_id INT NOT NULL,
                inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
                fin DATETIME NULL,
                resultado ENUM('correcta','incorrecta','timeout') NULL,
                duracion_segundos INT NULL,
                INDEX idx_usuario_pregunta (usuario_id, pregunta_id),
                INDEX idx_partida (partida_id),
                FOREIGN KEY (partida_id) REFERENCES partidas(id) ON DELETE CASCADE,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                FOREIGN KEY (pregunta_id) REFERENCES preguntas(id) ON DELETE CASCADE
            )";
            $this->conexion->query($ddl);
        }
    } // Crea la tabla preguntas_tiempos si no existe.
    // Esta tabla se usa para rastrear los tiempos de respuesta por pregunta dentro de una partida.

    public function registrarInicioPreguntaTiempo($partidaId, $usuarioId, $preguntaId) {
        $stmt = $this->conexion->prepare("INSERT INTO preguntas_tiempos (partida_id, usuario_id, pregunta_id) VALUES (?,?,?)");
        $stmt->bind_param("iii", $partidaId, $usuarioId, $preguntaId);
        $stmt->execute();
        return $this->conexion->insert_id;
    } // Inserta un registro en preguntas_tiempos marcando el inicio de la respuesta a una pregunta.

    public function cerrarPreguntaTiempo($usuarioId, $preguntaId, $resultado) {
        $stmt = $this->conexion->prepare("UPDATE preguntas_tiempos SET fin = NOW(), resultado = ?, duracion_segundos = TIMESTAMPDIFF(SECOND, inicio, NOW()) WHERE usuario_id = ? AND pregunta_id = ? AND fin IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("sii", $resultado, $usuarioId, $preguntaId);
        $stmt->execute();
    } // Cierra el registro de tiempo, marcando la hora de finalización
    // (fin = NOW()), el resultado (correcta, incorrecta, timeout) y calculando la duracion_segundos.

    public function excedioTiempo($usuarioId, $preguntaId, $limiteSegundos) {
        $stmt = $this->conexion->prepare("SELECT inicio FROM preguntas_tiempos WHERE usuario_id = ? AND pregunta_id = ? AND fin IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ii", $usuarioId, $preguntaId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if (!$res) return false; // si no hay registro no forzamos timeout
        $inicio = strtotime($res['inicio']);
        $ahora = time();
        return ($ahora - $inicio) > $limiteSegundos;
    } // Comprueba si el tiempo transcurrido desde el inicio de la pregunta
    // ha superado un límite de segundos especificado.

    public function obtenerSegundosTranscurridos($usuarioId, $preguntaId) {
        $stmt = $this->conexion->prepare("SELECT inicio FROM preguntas_tiempos WHERE usuario_id = ? AND pregunta_id = ? AND fin IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ii", $usuarioId, $preguntaId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if (!$res) return null;
        $inicio = strtotime($res['inicio']);
        return time() - $inicio;
    } // Devuelve el tiempo transcurrido en segundos desde que se inició la pregunta actual.

    public function getMetricasUsuario($usuarioId) {
        $stmt = $this->conexion->prepare("SELECT COALESCE(SUM(puntaje),0) AS total_puntaje FROM partidas WHERE usuario_id = ?");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $puntajeRow = $stmt->get_result()->fetch_assoc();
        $puntajeHistorico = (int)($puntajeRow['total_puntaje'] ?? 0);
        $stmt2 = $this->conexion->prepare("SELECT correcta FROM preguntas_usuarios WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 10");
        $stmt2->bind_param("i", $usuarioId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $totalRecientes = 0; $aciertosRecientes = 0;
        while ($r = $result2->fetch_assoc()) {
            $totalRecientes++;
            if ((int)$r['correcta'] === 1) $aciertosRecientes++;
        }
        $ratioReciente = $totalRecientes > 0 ? ($aciertosRecientes / $totalRecientes) : 0.0;

        $stmt3 = $this->conexion->prepare("SELECT COUNT(*) AS total, SUM(correcta) AS aciertos FROM preguntas_usuarios WHERE usuario_id = ?");
        $stmt3->bind_param("i", $usuarioId);
        $stmt3->execute();
        $row3 = $stmt3->get_result()->fetch_assoc();
        $totalGlobal = (int)($row3['total'] ?? 0);
        $aciertosGlobal = (int)($row3['aciertos'] ?? 0);
        $ratioGlobal = $totalGlobal > 0 ? ($aciertosGlobal / $totalGlobal) : 0.0;

        return [
            'puntaje_historico' => $puntajeHistorico,
            'ratio_reciente' => $ratioReciente, // 0..1
            'total_global' => $totalGlobal,
            'ratio_global' => $ratioGlobal,
            'total_recientes' => $totalRecientes
        ];
    } // Recopila las métricas de rendimiento del usuario necesarias para el algoritmo adaptativo:
    // • Puntaje histórico total.
    // • Ratio de aciertos en las últimas 10 respuestas.
    // • Totales y ratio de aciertos globales.

    public function obtenerPreguntaAdaptativaPorCategoriaId($categoriaId, $usuarioId) {

        $stmt = $this->conexion->prepare("SELECT p.* FROM preguntas p LEFT JOIN preguntas_usuarios pu ON pu.pregunta_id = p.id AND pu.usuario_id = ? WHERE p.categoria_id = ? AND pu.pregunta_id IS NULL AND p.activa = 1");
        $stmt->bind_param("ii", $usuarioId, $categoriaId);
        $stmt->execute();
        $res = $stmt->get_result();
        $faciles = []; $medias = []; $dificiles = [];
        while ($p = $res->fetch_assoc()) {

            $p['respuestas'] = [
                ['id'=>1,'texto'=>$p['respuesta_1']],
                ['id'=>2,'texto'=>$p['respuesta_2']],
                ['id'=>3,'texto'=>$p['respuesta_3']],
                ['id'=>4,'texto'=>$p['respuesta_4']]
            ];
            $nivel = $p['nivel_dificultad'] ?? 'Medio';
            switch ($nivel) {
                case 'Fácil': $faciles[] = $p; break;
                case 'Difícil': $dificiles[] = $p; break;
                default: $medias[] = $p; break;
            }
        }
        if (empty($faciles) && empty($medias) && empty($dificiles)) {
            return null;
        }

        $m = $this->getMetricasUsuario($usuarioId);
        $H = $m['puntaje_historico'];
        $R = $m['ratio_reciente'];
        $totalGlobal = $m['total_global'];

        if ($H < 50) {
            $pf = 0.60; $pm = 0.30; $pd = 0.10;
        } elseif ($H < 150) {
            $pf = 0.40; $pm = 0.40; $pd = 0.20;
        } elseif ($H < 300) {
            $pf = 0.30; $pm = 0.45; $pd = 0.25;
        } else {
            $pf = 0.20; $pm = 0.45; $pd = 0.35;
        }

        if ($totalGlobal < 5) {
            $pf = 0.60; $pm = 0.30; $pd = 0.10;
        }

        if ($R >= 0.80) {

            $delta = 0.05;
            if ($pf > $delta) { $pf -= $delta; $pd += $delta; }
            else { $pm -= $delta; $pd += $delta; }
        } elseif ($R < 0.50 && $totalGlobal >= 5) {
            $delta = 0.05;
            if ($pd > $delta) { $pd -= $delta; $pf += $delta; }
        }
        if ($R < 0.30 && $H >= 150) {
            $pf = max($pf, 0.50);
            $resto = 1 - $pf;
            $ratioMedio = 0.7;
            $pm = $resto * $ratioMedio;
            $pd = $resto - $pm;
        }

        $pdMax = ($R >= 0.90) ? 0.45 : 0.40;
        if ($pd > $pdMax) {
            $exceso = $pd - $pdMax; $pd = $pdMax; $pf += $exceso;
        }

        $suma = $pf + $pm + $pd;
        if ($suma != 1.0 && $suma > 0) { $pf /= $suma; $pm /= $suma; $pd /= $suma; }

        $r = mt_rand() / mt_getrandmax();
        $bucket = 'Fácil';
        if ($r < $pf) { $bucket = 'Fácil'; }
        elseif ($r < $pf + $pm) { $bucket = 'Medio'; }
        else { $bucket = 'Difícil'; }

        switch ($bucket) {
            case 'Fácil': $lista = $faciles; break;
            case 'Medio': $lista = $medias; break;
            case 'Difícil': $lista = $dificiles; break;
            default: $lista = $medias; break;
        }

        if (empty($lista)) {
            if (!empty($medias)) $lista = $medias;
            elseif (!empty($faciles)) $lista = $faciles;
            else $lista = $dificiles;
        }

        $count = count($lista);
        $idx = $count > 1 ? rand(0, $count - 1) : 0;
        return $lista[$idx];
    } // Implementa el algoritmo de dificultad adaptativa.
     // 1. Clasifica las preguntas disponibles (no respondidas) en tres buckets: Fácil, Medio, Difícil.
    // 2. Calcula las probabilidades (pf, pm, pd) de seleccionar una pregunta de cada bucket basándose en:
    // a) Puntaje Histórico y b) Ratio de Aciertos Reciente (R).
    // 3. Aplica ajustes dinámicos (ej: si R >= 0.80, aumenta la probabilidad de preguntas Difíciles).
    // 4. Selecciona un bucket al azar basado en las probabilidades calculadas y
    // devuelve una pregunta aleatoria de ese grupo.
}