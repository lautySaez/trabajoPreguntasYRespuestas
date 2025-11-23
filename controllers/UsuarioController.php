<?php
class UsuarioController
{
    private $usuarioModel;
    private $base_url = "http://localhost/trabajoPreguntasYRespuestas/";

    public function __construct($usuarioModel)
    {
        $this->usuarioModel = $usuarioModel;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    protected function render($viewName, $data = []) {
        extract($data);
        $viewPath = __DIR__ . "/../views/" . $viewName . ".php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Error: Vista no encontrada ($viewName).";
        }
    }
    public function perfil()
    {
        $usuario = $_SESSION['usuario'] ?? null;

        if (!$usuario) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit;
        }

        $datos_perfil = $this->usuarioModel->obtenerPerfilPublico($usuario['nombre_usuario']);

        $url_publica = $this->base_url . "/index.php?controller=UsuarioController&method=publico&username=" . urlencode($usuario['nombre_usuario']);

        $data = [
            'usuario' => $usuario,
            'datos_perfil' => $datos_perfil,
            'qr_url' => $url_publica
        ];

        $this->render('perfil', $data);
    }

    public function publico() {
        $username = $_GET['username'] ?? null;

        if (!$username) {
            $this->render('errorView', ['mensaje' => 'Usuario no especificado.']);
            return;
        }

        $datos = $this->usuarioModel->obtenerPerfilPublico($username);

        if (!$datos) {
            $this->render('errorView', ['mensaje' => 'El perfil de este usuario no está disponible o no existe.']);
            return;
        }

        $this->render('perfilPublico', ['datos' => $datos]);
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
        $this->render("configurar_perfil", ['usuario' => $usuario]);
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

        $this->render("elegir_avatar", ['usuario' => $usuario]);
    }

}