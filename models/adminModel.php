<?php
class adminModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    public function contarUsuarios() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE rol != 'Administrador'");
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }
    public function obtenerUsuarios($limit = 500) {
        $stmt = $this->conexion->prepare("SELECT id, nombre, nombre_usuario, email, rol, estado_registro, foto_perfil, fecha_registro, pais, ciudad, fecha_nacimiento 
                                          FROM usuarios 
                                          WHERE rol != 'Administrador' 
                                          ORDER BY fecha_registro DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function actualizarEstadoUsuario($userId, $estado) {
        $sql = "UPDATE usuarios SET estado_registro = ? WHERE id = ? AND rol != 'Administrador'";
        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            error_log("Error al preparar 'actualizarEstadoUsuario': " . $this->conexion->error);
            return 0;
        }
        $stmt->bind_param("si", $estado, $userId);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function eliminarUsuario($userId) {
        $sql = "DELETE FROM usuarios WHERE id = ? AND rol != 'Administrador'";
        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            error_log("Error al preparar 'eliminarUsuario': " . $this->conexion->error);
            return 0;
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->affected_rows;
    }
    public function actualizarRolUsuario($userId, $nuevoRol) {
        if (!in_array($nuevoRol, ['Jugador', 'Editor'])) {
            return 0;
        }

        $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            error_log("Error al preparar 'actualizarRolUsuario': " . $this->conexion->error);
            return 0;
        }
        $stmt->bind_param("si", $nuevoRol, $userId);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function contarPreguntas() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) AS total FROM preguntas");
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }

    public function preguntasPorCategoria() {
        $sql = "SELECT c.id, c.nombre, COUNT(p.id) AS total
                FROM categorias c
                LEFT JOIN preguntas p ON p.categoria_id = c.id
                GROUP BY c.id, c.nombre
                ORDER BY total DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function top10PreguntasMasFaciles() {
        $sql = "SELECT id,
                   pregunta,
                   COALESCE(porcentaje_acierto, 0) AS porcentaje_acierto,
                   nivel_dificultad
            FROM preguntas
            ORDER BY COALESCE(porcentaje_acierto, 0) DESC
            LIMIT 10";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function mejoresJugadores($limit = 10) {
        $sql = "SELECT u.id, u.nombre, u.nombre_usuario, u.foto_perfil, IFNULL(SUM(pt.puntaje),0) AS total_puntos, COUNT(pt.id) AS partidas_jugadas
                FROM usuarios u
                LEFT JOIN partidas pt ON pt.usuario_id = u.id
                WHERE u.rol = 'Jugador'
                GROUP BY u.id, u.nombre, u.nombre_usuario, u.foto_perfil
                ORDER BY total_puntos DESC
                LIMIT ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function contarPartidas() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) AS total FROM partidas");
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }
    public function lugaresDondeJuegan($limit = 50) {
        $sql = "SELECT 
                    COALESCE(pais,'Desconocido') AS pais, 
                    COALESCE(ciudad,'Desconocido') AS ciudad, 
                    COUNT(id) AS cantidad
                FROM usuarios
                WHERE pais IS NOT NULL AND ciudad IS NOT NULL AND pais != '' AND ciudad != ''
                GROUP BY pais, ciudad
                ORDER BY cantidad DESC
                LIMIT ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function distribucionEdades() {
        $sql = "SELECT
                    CASE
                        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 13 THEN '<13'
                        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 13 AND 17 THEN '13-17'
                        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 18 AND 24 THEN '18-24'
                        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 25 AND 34 THEN '25-34'
                        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 35 AND 44 THEN '35-44'
                        WHEN TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN 45 AND 64 THEN '45-64'
                        ELSE '65+'
                    END AS rango,
                    COUNT(*) AS cantidad
                FROM usuarios
                GROUP BY rango
                ORDER BY FIELD(rango, '<13','13-17','18-24','25-34','35-44','45-64','65+')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function distribucionGenero() {
        $sql = "SELECT sexo AS genero, COUNT(*) AS cantidad FROM usuarios GROUP BY sexo";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerInformes($limit = 200) {
        $sql = "SELECT ip.id, ip.pregunta_id, ip.editor_id, u.nombre AS editor_nombre, ip.tipo_accion, ip.motivo, ip.fecha, ip.pregunta AS pregunta_texto, ip.r1, ip.r2, ip.r3, ip.r4, ip.correcta, ip.categoria_id
                FROM InformePreguntas ip
                LEFT JOIN usuarios u ON u.id = ip.editor_id
                ORDER BY ip.fecha DESC
                LIMIT ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerReportesJugadores($limit = 200) {
        $sql = "SELECT r.id, r.pregunta_id, r.usuario_id, u.nombre AS usuario_nombre, r.motivo, r.fecha_reporte, q.pregunta AS pregunta_texto
                FROM reportes r
                LEFT JOIN usuarios u ON u.id = r.usuario_id
                LEFT JOIN preguntas q ON q.id = r.pregunta_id
                ORDER BY r.fecha_reporte DESC
                LIMIT ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerDetalleReportePorId($id) {
        $sql = "SELECT r.id, r.pregunta_id, r.usuario_id, u.nombre AS usuario_nombre, u.email AS usuario_email, 
                   r.motivo, r.fecha_reporte, q.pregunta AS pregunta_texto, q.respuesta_correcta, 
                   q.respuesta_1 AS respuesta1, q.respuesta_2 AS respuesta2, q.respuesta_3 AS respuesta3, q.respuesta_4 AS respuesta4
            FROM reportes r
            LEFT JOIN usuarios u ON u.id = r.usuario_id
            LEFT JOIN preguntas q ON q.id = r.pregunta_id
            WHERE r.id = ?";

        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            error_log("Error de MySQL al preparar detalle de reporte: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $detalle = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $detalle;
    }

    public function exportUsuariosCSVData() {
        return $this->obtenerUsuarios(10000);
    }

}