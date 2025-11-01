<?php
class UsuarioController
{
    private $usuarioModel;

    public function __construct($usuarioModel)
    {
        $this->usuarioModel = $usuarioModel;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function perfil()
    {
        $usuario = $_SESSION['usuario'] ?? null;

        if (!$usuario) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit;
        }

        include("views/perfil.php");
    }

    public function confirmarPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=UsuarioController&method=perfil");
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        $usuarioSesion = $_SESSION['usuario'] ?? null;
        if (!$usuarioSesion) {

            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit;
        }

        $password = $_POST['password_actual'] ?? '';

        $usuarioBD = $this->usuarioModel->obtenerPorId($usuarioSesion['id']);

        if (!$usuarioBD) {
            $_SESSION['error'] = "Usuario no encontrado.";
            header("Location: index.php?controller=UsuarioController&method=perfil");
            exit;
        }

        if (password_verify($password, $usuarioBD['password'])) {
            // password correcto -> permitir edición y redirigir
            $_SESSION['permitir_configuracion'] = true;
            header("Location: index.php?controller=UsuarioController&method=configurarPerfil");
            exit;
        } else {
            $_SESSION['error'] = "Contraseña actual incorrecta.";
            header("Location: index.php?controller=UsuarioController&method=perfil");
            exit;
        }
    }

    public function configurarPerfil()
    {
        if (empty($_SESSION['permitir_configuracion'])) {
            header("Location: index.php?controller=UsuarioController&method=perfil");
            exit;
        }

        $usuario = $_SESSION['usuario'];
        include("views/configurar_perfil.php");
    }

    public function actualizarPerfil()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_SESSION['usuario'];
            $id = $usuario['id'];

            $nombre_usuario = trim($_POST['nombre_usuario']);
            $email = trim($_POST['email']);
            $password = $_POST['password'] ?? null;
            $repassword = $_POST['repassword'] ?? null;

            if ($password && $password !== $repassword) {
                $_SESSION['error'] = "Las contraseñas no coinciden.";
                header("Location: index.php?controller=UsuarioController&method=configurarPerfil");
                exit;
            }

            $foto_perfil = $_FILES['foto_perfil']['name'] ?? null;
            $ruta_avatar = null;

            if ($foto_perfil) {
                $uploadDir = "uploads/";
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
                $ruta_avatar = $uploadDir . basename($foto_perfil);
                move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_avatar);
            }

            $this->usuarioModel->actualizarPerfil($id, $nombre_usuario, $email, $password, $ruta_avatar);
            $_SESSION['usuario'] = $this->usuarioModel->obtenerPorId($id);

            unset($_SESSION['permitir_configuracion']);
            header("Location: index.php?controller=UsuarioController&method=perfil");
            exit;
        }
    }

    public function elegirAvatar()
    {
        if (empty($_SESSION['permitir_configuracion'])) {
            header("Location: index.php?controller=UsuarioController&method=perfil");
            exit;
        }

        $usuario = $_SESSION['usuario'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $avatar = $_POST['foto_perfil'] ?? null;
            if ($avatar) {
                // Actualizamos solo el avatar, manteniendo los demás campos
                $this->usuarioModel->actualizarPerfil(
                    $usuario['id'],
                    $usuario['nombre_usuario'],
                    $usuario['email'],
                    null,
                    $avatar
                );
                $_SESSION['usuario'] = $this->usuarioModel->obtenerPorId($usuario['id']);
            }

            header("Location: index.php?controller=UsuarioController&method=configurarPerfil");
            exit;
        }

        include("views/elegir_avatar.php");
    }

}