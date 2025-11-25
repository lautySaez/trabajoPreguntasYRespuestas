<?php
class Usuario
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrarUsuario(
        $nombre,
        $fecha_nacimiento,
        $sexo,
        $pais,
        $ciudad,
        $email,
        $password,
        $nombre_usuario,
        $foto_perfil = null,
        $estado_registro = "Inactivo",
        $token_activacion
    )
    {
        $query = "SELECT * FROM usuarios WHERE email = ? OR nombre_usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $email, $nombre_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $rol = "jugador";

    $query = "INSERT INTO usuarios (nombre, fecha_nacimiento, sexo, pais, ciudad, email, password, nombre_usuario, rol, foto_perfil, estado_registro, token_verificacion)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conexion->prepare($query);
    $stmt->bind_param("sssssssssssi", $nombre, $fecha_nacimiento, $sexo, $pais, $ciudad, $email, $passwordHash, $nombre_usuario, $rol, $foto_perfil, $estado_registro, $token_activacion);

        return $stmt->execute();
    }

    public function login($nombre_usuario, $password)
    {
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($password, $usuario['password'])) {
                return $usuario;
            }
        }
        return false;
    }

    public function actualizarAvatar($nombre_usuario, $foto_perfil)
    {
        $sql = "UPDATE usuarios SET foto_perfil = ? WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $foto_perfil, $nombre_usuario);
        return $stmt->execute();
    }

    public function obtenerPorNombreUsuario($nombre_usuario)
    {
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function validarRegistrarUsuario($nombre_usuario, $password, $token_activacion)
    {
        $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            if (password_verify($password, $usuario['password'])) {
                $query = "UPDATE usuarios SET estado_registro = 'Activo' WHERE nombre_usuario = ? AND token_verificacion = ?";
                $stmt = $this->conexion->prepare($query);
                $stmt->bind_param("ss", $nombre_usuario, $token_activacion);
                return $stmt->execute();
            }
        }
    }

    public function actualizarPerfil($id, $nombre_usuario, $email, $password = null, $foto_perfil = null)
    {
        $campos = [];
        $params = [];

        $campos[] = "nombre_usuario = ?";
        $params[] = $nombre_usuario;

        $campos[] = "email = ?";
        $params[] = $email;

        if ($password) {
            $campos[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($foto_perfil) {
            $campos[] = "foto_perfil = ?";
            $params[] = $foto_perfil;
        }

        $params[] = $id;

        $sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE id = ?";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            die("Error al preparar: " . $this->conexion->error);
        }

        $tipos = str_repeat("s", count($params) - 1) . "i";
        $stmt->bind_param($tipos, ...$params);

        if (!$stmt->execute()) {
            die("Error al ejecutar: " . $stmt->error);
        }

        return true;
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function obtenerTodosLosUsuarios()
    {
    $conexion = new MyConexion("localhost", "root", "", "preguntas_respuestas");
    $conn = $conexion->getConexion();

    $sql = "SELECT foto_perfil, nombre_usuario, email, rol, estado_registro FROM usuarios";
    $resultado = $conn->query($sql);

    if (!$resultado) {
        die("Error en la consulta: " . $conn->error);
    }
    
    $usuarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }

    return $usuarios;
    }


    public function obtenerPerfilPublico($username)
    {
        $sql = "SELECT 
                u.id, 
                u.nombre_usuario, 
                u.foto_perfil,
                IFNULL(SUM(p.puntaje), 0) AS puntos_totales,
                COUNT(p.id) AS partidas_jugadas
            FROM usuarios u
            LEFT JOIN partidas p ON p.usuario_id = u.id
            WHERE u.nombre_usuario = ? AND u.estado_registro != 'Bloqueado'
            GROUP BY u.id, u.nombre_usuario, u.foto_perfil";

        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error al preparar 'obtenerPerfilPublico': " . $this->conexion->error);
            return null;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function actualizarEstadisticasJugador(int $usuarioId, bool $acierto): void {
        $this->conexion->begin_transaction();
        try {
            $stmt = $this->conexion->prepare("
                UPDATE usuarios
                SET total_vistas = total_vistas + 1,
                    total_aciertos = total_aciertos + ?
                WHERE id = ?
            ");
            $aciertoInt = $acierto ? 1 : 0;
            $stmt->bind_param("ii", $aciertoInt, $usuarioId);
            $stmt->execute();
            $stmt->close();

            $this->recalcularNivelJugador($usuarioId);

            $this->conexion->commit();
        } catch (Exception $e) {
            $this->conexion->rollback();
            error_log("Error actualizarEstadisticasJugador: " . $e->getMessage());
        }
    }

    public function recalcularNivelJugador(int $usuarioId): void
    {
        $stmt = $this->conexion->prepare("
            SELECT total_vistas, total_aciertos
            FROM usuarios
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $res = $stmt->get_result();
        $fila = $res->fetch_assoc();
        $stmt->close();

        if (!$fila) return;

        $vistas = (int)($fila['total_vistas'] ?? 0);
        $aciertos = (int)($fila['total_aciertos'] ?? 0);

        // Jugador nuevo: mantener 'Normal' por default
        if ($vistas === 0) {
            $nivel = 'Normal';
        } else {
            $ratio = $aciertos / $vistas;
            if ($ratio < 0.3) $nivel = 'Newbie';
            elseif ($ratio < 0.6) $nivel = 'Normal';
            else $nivel = 'Pro';
        }

        $stmt2 = $this->conexion->prepare("UPDATE usuarios SET nivel_jugador = ? WHERE id = ?");
        $stmt2->bind_param("si", $nivel, $usuarioId);
        $stmt2->execute();
        $stmt2->close();
    }

    public function obtenerNivelJugador(int $usuarioId): ?string
    {
        $stmt = $this->conexion->prepare("SELECT nivel_jugador FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $res = $stmt->get_result();
        $fila = $res->fetch_assoc();
        $stmt->close();
        return $fila['nivel_jugador'] ?? null;
    }
}