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
    } // Registra un nuevo usuario.
    // Primero verifica que el email y el nombre_usuario no existan.
    // Si son únicos, encripta la contraseña, asigna el rol "jugador", establece el estado a "Inactivo"
    // y guarda un token_activacion para la verificación posterior.

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
    } // Procesa el inicio de sesión.
    // Busca al usuario por nombre_usuario y verifica la contraseña encriptada.
    // Si las credenciales son correctas, devuelve el array del usuario; de lo contrario, devuelve false.

    public function actualizarAvatar($nombre_usuario, $foto_perfil)
    {
        $sql = "UPDATE usuarios SET foto_perfil = ? WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $foto_perfil, $nombre_usuario);
        return $stmt->execute();
    } // Actualiza únicamente la URL o ruta de la foto de perfil de un usuario.

    public function obtenerPorNombreUsuario($nombre_usuario)
    {
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    } // Obtiene todos los datos de un usuario buscando por su nombre de usuario único.

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
    } // Activa la cuenta del usuario.
    // Verifica el nombre_usuario y la password (por seguridad),
    // y si coinciden con el token_activacion, cambia el estado_registro del usuario a 'Activo'.

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
    } // Actualiza los datos básicos del perfil (nombre de usuario, email).
    // Permite opcionalmente actualizar la contraseña (encriptándola) y/o la foto de perfil.
    // Utiliza lógica dinámica para construir la consulta UPDATE solo con los campos proporcionados.

    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    } // Obtiene todos los datos de un usuario buscando por su ID único.

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
    } //  // Recupera una lista básica de todos los usuarios (perfil, nombre, email, rol, estado de registro).

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
    } // Obtiene los datos de perfil para su visualización pública,
    // incluyendo el cálculo del total de puntos (SUM(p.puntaje)) y el número de partidas jugadas,
    // siempre y cuando la cuenta no esté 'Bloqueada'.

    public function actualizarEstadisticasJugador(int $usuarioId, bool $acierto): void
    {
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
    } // Actualiza las estadísticas de juego del usuario dentro de una
    // transacción de base de datos (para asegurar la atomicidad).
    // 1. Incrementa total_vistas en 1.
    // 2. Incrementa total_aciertos en 1 solo si $acierto es true.
    // 3. Llama a recalcularNivelJugador().

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
    } // Determina y actualiza el nivel_jugador basándose en el ratio de aciertos
     // sobre el total de vistas (total_aciertos / total_vistas).
    //* Newbie: Ratio < 0.3
   //* Normal: 0.3 <= Ratio < 0.6
  //* Pro: Ratio >= 0.6

    public function obtenerNivelJugador(int $usuarioId): ? string
    {
        $stmt = $this->conexion->prepare("SELECT nivel_jugador FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $res = $stmt->get_result();
        $fila = $res->fetch_assoc();
        $stmt->close();
        return $fila['nivel_jugador'] ?? null;
    } // Devuelve el nivel de jugador (Newbie, Normal, Pro) asignado al usuario.
}