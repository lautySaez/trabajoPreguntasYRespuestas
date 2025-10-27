<?php
class LoginController
{
    private $usuarioModel;

    public function __construct($usuarioModel)
    {
        $this->usuarioModel = $usuarioModel;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function inicioSesion()
    {
        $this->loginPage();
    }

    public function loginPage()
    {
        if (isset($_SESSION["usuario"])) {
            $rol = $_SESSION["usuario"]["rol"];
            $this->redirectHomePorRol($rol);
            return;
        }
        include("views/inicioSesion.php");
    }

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre_usuario = trim($_POST["nombre_usuario"]);
            $password = trim($_POST["password"]);

            $usuario = $this->usuarioModel->login($nombre_usuario, $password);

            if ($usuario) {
                $_SESSION["usuario"] = $usuario;
                $this->redirectHomePorRol($usuario["rol"]);
                return;
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }

        include("views/inicioSesion.php");
    }

    private function redirectHomePorRol($rol)
    {
        switch ($rol) {
            case "admin":
                $this->homeAdmin();
                break;
            case "editor":
                $this->homeEditor();
                break;
            case "jugador":
            default:
                $this->home();
                break;
        }
    }

    public function registro()
    {
        include("views/registro.php");
    }

    public function registrarUsuario()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = trim($_POST["nombre"]);
            $fecha_nacimiento = $_POST["fecha_nacimiento"];
            $sexo = $_POST["sexo"];
            $pais = trim($_POST["pais"]);
            $ciudad = trim($_POST["ciudad"]);
            $email = trim($_POST["email"]);
            $password = $_POST["password"];
            $repassword = $_POST["repassword"];
            $nombre_usuario = trim($_POST["nombre_usuario"]);

            if ($password !== $repassword) {
                $error = "Las contraseñas no coinciden.";
                include("views/registro.php");
                return;
            }

            if (strlen($password) < 4 || strlen($password) > 10) {
                $error = "La contraseña debe tener entre 4 y 10 caracteres.";
                include("views/registro.php");
                return;
            }

            $edad = date_diff(date_create($fecha_nacimiento), date_create('today'))->y;
            if ($edad < 10 || $edad > 100) {
                $error = "La fecha de nacimiento no es válida.";
                include("views/registro.php");
                return;
            }

            $foto_perfil = null;

            $exito = $this->usuarioModel->registrarUsuario(
                $nombre,
                $fecha_nacimiento,
                $sexo,
                $pais,
                $ciudad,
                $email,
                $password,
                $nombre_usuario,
                $foto_perfil
            );

            if ($exito) {
                header("Location: index.php?controller=LoginController&method=elegirAvatar&usuario=" . urlencode($nombre_usuario));
                exit();
            } else {
                $error = "El usuario o el email ya existen.";
            }
        }

        include("views/registro.php");
    }

    public function elegirAvatar()
    {
        // Validación: si no viene nombre de usuario, redirigir a registro
        $nombre_usuario = $_GET["usuario"] ?? null;
        if (!$nombre_usuario) {
            header("Location: index.php?controller=LoginController&method=registro");
            exit();
        }

        include("views/elegir_avatar.php");
    }

    public function guardarAvatar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre_usuario = $_POST["nombre_usuario"];
            $foto_perfil = $_POST["foto_perfil"] ?? null;

            if ($foto_perfil) {
                $this->usuarioModel->actualizarAvatar($nombre_usuario, $foto_perfil);
            }

            // Redirigir al login
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit();
        }
    }

    public function home()
    {
        if (!isset($_SESSION["usuario"])) {
            include("views/inicioSesion.php");
            return;
        }
        include("views/home.php");
    }

    public function homeAdmin()
    {
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
            include("views/inicioSesion.php");
            return;
        }
        include("views/homeAdmin.php");
    }

    public function homeEditor()
    {
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "editor") {
            include("views/inicioSesion.php");
            return;
        }
        include("views/homeEditor.php");
    }

    public function logout()
    {
        session_destroy();
        include("views/inicioSesion.php");
    }
}