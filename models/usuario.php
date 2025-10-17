<?php
class Usuario
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function registrarUsuario($nombre, $anio_nacimiento, $sexo, $pais, $ciudad, $email, $password, $nombre_usuario, $foto_perfil = null)
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

        $query = "INSERT INTO usuarios (nombre, anio_nacimiento, sexo, pais, ciudad, email, password, nombre_usuario, foto_perfil)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sisssssss", $nombre, $anio_nacimiento, $sexo, $pais, $ciudad, $email, $passwordHash, $nombre_usuario, $foto_perfil);
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
}

