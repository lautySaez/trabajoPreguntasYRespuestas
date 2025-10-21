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
            $usuario = $_SESSION["usuario"];
            $rol = $usuario["rol"];

            switch ($rol) {
                case "admin":
                    include("views/homeAdmin.php");
                    return;
                case "editor":
                    include("views/homeEditor.php");
                    return;
                case "jugador":
                default:
                    include("views/home.php");
                    return;
            }
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
                $rol = $usuario["rol"];

                switch ($rol) {
                    case "admin":
                        $this->homeAdmin();
                        return;
                    case "editor":
                        $this->homeEditor();
                        return;
                    case "jugador":
                    default:
                        $this->home();
                        return;
                }
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }

        include("views/inicioSesion.php");
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

            $foto_perfil = null;
            if (!empty($_FILES["foto_perfil"]["name"])) {
                $uploadDir = "uploads/";
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $foto_perfil = $uploadDir . basename($_FILES["foto_perfil"]["name"]);
                move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $foto_perfil);
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
                $mensaje = "Usuario registrado correctamente. Ahora puedes iniciar sesión.";
                include("views/inicioSesion.php");
                return;
            } else {
                $error = "El usuario o el email ya existen.";
            }
        }

        include("views/registro.php");
    }

    public function home()
    {
        if (!isset($_SESSION["usuario"])) {
            include("views/inicioSesion.php");
            return;
        }
        $usuario = $_SESSION["usuario"];
        include("views/home.php");
    }

    public function homeAdmin()
    {
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "admin") {
            include("views/inicioSesion.php");
            return;
        }
        $usuario = $_SESSION["usuario"];
        include("views/homeAdmin.php");
    }

    public function homeEditor()
    {
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "editor") {
            include("views/inicioSesion.php");
            return;
        }
        $usuario = $_SESSION["usuario"];
        include("views/homeEditor.php");
    }

    public function logout()
    {
        session_destroy();
        include("views/inicioSesion.php");
    }
}