<?php
class adminModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function contarUsuarios() {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'Jugador'");
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return (int)($res['total'] ?? 0);
    }

    public function obtenerUsuarios($limit = 200) {
        $stmt = $this->conexion->prepare("SELECT id, nombre, nombre_usuario, email, rol, estado_registro, foto_perfil, fecha_registro, pais, ciudad, fecha_nacimiento FROM usuarios ORDER BY fecha_registro DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
        $sql = "SELECT id, pregunta, porcentaje_acierto, nivel_dificultad, veces_mostrada
                FROM preguntas
                WHERE veces_mostrada > 0
                ORDER BY porcentaje_acierto DESC
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
        $sql = "SELECT COALESCE(u.pais,'Desconocido') AS pais, COALESCE(u.ciudad,'Desconocido') AS ciudad, COUNT(pt.id) AS cantidad
                FROM partidas pt
                LEFT JOIN usuarios u ON u.id = pt.usuario_id
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
                FROM informePreguntas ip
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

    public function exportUsuariosCSVData() {
        return $this->obtenerUsuarios(10000);
    }

}