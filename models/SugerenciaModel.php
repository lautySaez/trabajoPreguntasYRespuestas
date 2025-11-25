<?php
require_once("helper/MyConexion.php");

class SugerenciaModel {
    private $conexion;

    public function __construct() {
        $db = new MyConexion("localhost", "root", "", "preguntas_respuestas");
        $this->conexion = $db->getConexion();
        $this->asegurarTablaSugerencias();
    }

    private function asegurarTablaSugerencias() {
        $res = $this->conexion->query("SHOW TABLES LIKE 'preguntas_sugeridas'");
        if ($res && $res->num_rows === 0) {
            $ddl = "CREATE TABLE preguntas_sugeridas (\n                id INT AUTO_INCREMENT PRIMARY KEY,\n                usuario_id INT NOT NULL,\n                categoria_id INT NOT NULL,\n                pregunta VARCHAR(255) NOT NULL,\n                respuesta_1 VARCHAR(255) NOT NULL,\n                respuesta_2 VARCHAR(255) NOT NULL,\n                respuesta_3 VARCHAR(255) NOT NULL,\n                respuesta_4 VARCHAR(255) NOT NULL,\n                respuesta_correcta TINYINT(1) NOT NULL,\n                estado ENUM('pendiente','aprobada','rechazada') DEFAULT 'pendiente',\n                fecha_sugerida DATETIME DEFAULT CURRENT_TIMESTAMP,\n                fecha_revision DATETIME NULL,\n                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,\n                FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE,\n                INDEX idx_estado (estado),\n                INDEX idx_usuario_fecha (usuario_id, fecha_sugerida)\n            )";
            $this->conexion->query($ddl);
        }
    }

    public function puedeSugerirHoy($usuarioId) {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) AS c FROM preguntas_sugeridas WHERE usuario_id = ? AND DATE(fecha_sugerida) = CURDATE()");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $c = $stmt->get_result()->fetch_assoc()['c'] ?? 0;
        return $c < 2; // máximo 2 por día
    }

    public function esCategoriaActiva($categoriaId) {
        $stmt = $this->conexion->prepare("SELECT activa FROM categorias WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $categoriaId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row && (int)$row['activa'] === 1;
    }

    public function existePreguntaExacta($texto) {
        $stmt = $this->conexion->prepare("SELECT 1 FROM preguntas WHERE pregunta = ? LIMIT 1");
        $stmt->bind_param("s", $texto);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) return true;
        $stmt2 = $this->conexion->prepare("SELECT 1 FROM preguntas_sugeridas WHERE pregunta = ? AND estado='pendiente' LIMIT 1");
        $stmt2->bind_param("s", $texto);
        $stmt2->execute();
        return $stmt2->get_result()->num_rows > 0;
    }

    public function crearSugerencia($usuarioId, $categoriaId, $pregunta, $r1, $r2, $r3, $r4, $correcta) {
        $stmt = $this->conexion->prepare("INSERT INTO preguntas_sugeridas (usuario_id, categoria_id, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, respuesta_correcta) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iisssssi", $usuarioId, $categoriaId, $pregunta, $r1, $r2, $r3, $r4, $correcta);
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    public function obtenerPendientes() {
        $res = $this->conexion->query("SELECT ps.*, c.nombre AS categoria_nombre, u.nombre_usuario FROM preguntas_sugeridas ps JOIN categorias c ON c.id = ps.categoria_id JOIN usuarios u ON u.id = ps.usuario_id WHERE ps.estado='pendiente' ORDER BY ps.fecha_sugerida DESC");
        $out = [];
        while ($row = $res->fetch_assoc()) $out[] = $row;
        return $out;
    }

    public function aprobar($id) {
        // Traer sugerencia
        $stmt = $this->conexion->prepare("SELECT * FROM preguntas_sugeridas WHERE id=? AND estado='pendiente' LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $sug = $stmt->get_result()->fetch_assoc();
        if (!$sug) return false;
        // Insertar en preguntas
        $stmt2 = $this->conexion->prepare("INSERT INTO preguntas (categoria_id, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, respuesta_correcta, activa) VALUES (?,?,?,?,?,?,?,1)");
        $stmt2->bind_param("isssssi", $sug['categoria_id'], $sug['pregunta'], $sug['respuesta_1'], $sug['respuesta_2'], $sug['respuesta_3'], $sug['respuesta_4'], $sug['respuesta_correcta']);
        $stmt2->execute();
        // Marcar sugerencia aprobada
        $stmt3 = $this->conexion->prepare("UPDATE preguntas_sugeridas SET estado='aprobada', fecha_revision=NOW() WHERE id=?");
        $stmt3->bind_param("i", $id);
        $stmt3->execute();
        return true;
    }

    public function rechazar($id) {
        $stmt = $this->conexion->prepare("UPDATE preguntas_sugeridas SET estado='rechazada', fecha_revision=NOW() WHERE id=? AND estado='pendiente'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function obtenerCategoriasActivas() {
        $res = $this->conexion->query("SELECT id, nombre FROM categorias WHERE activa=1 ORDER BY nombre");
        $cats = [];
        while ($row = $res->fetch_assoc()) $cats[] = $row;
        return $cats;
    }
}
