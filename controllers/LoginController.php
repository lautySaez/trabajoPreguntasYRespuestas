<?php

$autoload_paths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php',
    getcwd() . '/vendor/autoload.php'
];

$autoload_loaded = false;
foreach ($autoload_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoload_loaded = true;
        break;
    }
}

if (!$autoload_loaded) {
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class LoginController
{
    private $usuarioModel;
    private $partidaModel;  

    public function __construct($usuarioModel)
    {
        require_once("models/partidaModel.php");
        $this->usuarioModel = $usuarioModel;
        $this->partidaModel = new PartidaModel();

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
            // Usar los métodos específicos para cada rol para que
            // se carguen los datos necesarios (ej: últimas partidas del jugador).
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
                $rol = $usuario["rol"];
                $estado_registro = $usuario["estado_registro"];

                if ($estado_registro == "Activo") {
                    switch ($rol) {
                        case "Administrador":
                            $this->homeAdmin();
                            return;
                        case "Editor":
                            $this->homeEditor();
                            return;
                        case "Jugador":
                        default:
                            $this->home();
                            return;
                    }
                } else {
                    include("views/validarRegistroUsuario.php");
                    return;
                }
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        }

        include("views/inicioSesion.php");
    }

    private function redirectHomePorRol($rol)
    {
        switch ($rol) {
            case "Administrador":
                $this->homeAdmin();
                break;
            case "Editor":
                $this->homeEditor();
                break;
            case "Jugador":
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
            $error = null;
            $nombre = trim($_POST["nombre"]);
            $fecha_nacimiento = $_POST["fecha_nacimiento"];
            $sexo = $_POST["sexo"];
            $pais = trim($_POST["pais"]);
            $ciudad = trim($_POST["ciudad"]);
            $email = trim($_POST["email"]);
            $password = $_POST["password"];
            $repassword = $_POST["repassword"];
            $nombre_usuario = trim($_POST["nombre_usuario"]);
            $estado_registro = "Inactivo";
            $token_activacion = random_int(100000, 999999);

            if ($password !== $repassword) {
                $error = "Las contraseñas no coinciden.";
            } elseif (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,12}$/", $password)) {
                $error = "La contraseña debe tener entre 8 y 12 caracteres, incluir al menos una mayúscula, un número y un carácter especial.";
            } else {
                $edad = date_diff(date_create($fecha_nacimiento), date_create('today'))->y;
                if ($edad < 10 || $edad > 100) {
                    $error = "La fecha de nacimiento no es válida.";
                }
            }

            if (!$error) {
                $foto_perfil = null;
                if (!empty($_FILES["foto_perfil"]["name"])) {
                    $uploadDir = "uploads/";
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $foto_perfil = $uploadDir . basename($_FILES["foto_perfil"]["name"]);
                    move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $foto_perfil);
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
                    $estado_registro,
                    $token_activacion,
                    $foto_perfil
                );

                if ($exito) {
                    try {
                        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                            $mail = new PHPMailer(true);
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.gmail.com';
                            $mail->SMTPAuth   = true;
                            $mail->Username   = 'aciertayaa@gmail.com';
                            $mail->Password   = 'egnq wplg anyu plah';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 587;

                            $mail->setFrom('aciertayaa@gmail.com', 'AciertaYa');
                            $mail->addAddress($email, $nombre_usuario);

                            $mail->isHTML(true);
                            $mail->Subject = 'Bienvenido a AciertaYaa';
                            $mail->Body    = '<h1>Bienvenido a AciertaYaa!</h1><p>Por favor, ingrese el siguiente identificador para confirmar tu registro: <strong>' . $token_activacion . '</strong></p>';

                            $mail->send();
                        }
                    } catch (Exception $e) {
                        error_log("Error al enviar email: " . $e->getMessage());
                    }

                    include("views/registroExitoso.php");
                    return;
                } else {
                    $error = "El usuario o el email ya existen.";
                }
            }

            include("views/registro.php");
            return;
        }

        include("views/registro.php");
    }



    public function validarRegistrarUsuario()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre_usuario = $_POST["nombre_usuario"];
            $password = $_POST["password"];
            $token_activacion = $_POST["token"];

            $validarUsuario = $this->usuarioModel->login($nombre_usuario, $password);

            if ($validarUsuario) {

                $exito = $this->usuarioModel->validarRegistrarUsuario(
                    $nombre_usuario,
                    $password,
                    $token_activacion
                );

                if ($exito) {
                    header("Location: /login/elegirAvatar?usuario=" . urlencode($nombre_usuario));
                    exit();
                } else {
                    $error = "El usuario o el email ya existen.";
                }
            }
            include("views/validarRegistroUsuario.php");
            return;
        }
        include("views/validarRegistroUsuario.php");
    }

    public function elegirAvatar()
    {
        $usuario = $_SESSION['usuario'] ?? null;

        if (!$usuario) {
            header("Location: /login");
            exit();
        }

        $nombre_usuario = $usuario['nombre_usuario'];
        include("views/elegir_avatar.php");
    }


    public function guardarAvatar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario = $_SESSION['usuario'] ?? null;

            if (!$usuario) {
                header("Location: /login");
                exit();
            }

            $nombre_usuario = $usuario['nombre_usuario'];
            $foto_perfil = $_POST['foto_perfil'] ?? null;

            if ($foto_perfil) {
                $this->usuarioModel->actualizarAvatar($nombre_usuario, $foto_perfil);

                $_SESSION['usuario']['foto_perfil'] = $foto_perfil;
            }

            header("Location: /home");
            exit();
        }
    }

    public function home() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["usuario"])) {
            include("views/inicioSesion.php");
            return;
        }

        $usuario = $_SESSION["usuario"];
        $ultimaPartida = null;

        $rol = strtolower($usuario['rol'] ?? '');

        if ($rol === 'jugador') {
            $ultimasPartidas = $this->partidaModel->getUltimasPartidas($usuario['id'], 5);
        }

        $datos = [
            'usuario' => $usuario,
            'ultimasPartidas' => $ultimasPartidas
        ];

        extract($datos);
        switch ($rol) {
            case 'jugador':
                include("views/home.php");
                break;
            case 'editor':
                include("views/home_editor.php");
                break;
            case 'admin':
                include("views/home_admin.php");
                break;
            default:
                include("views/inicioSesion.php");
                break;
        }
    }

    public function homeAdmin()
    {
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Administrador") {
            include("views/inicioSesion.php");
            return;
        }

        $usuario = $_SESSION["usuario"];

        $config = parse_ini_file("config/config.ini");
        require_once("models/usuario.php");
        require_once("helper/MyConexion.php");
        $conexion = new MyConexion($config['server'], $config['user'], $config['pass'], $config['database']);
        $conn = $conexion->getConexion();

        $modelUsuario = new Usuario($conn);
        $usuarios = $modelUsuario->obtenerTodosLosUsuarios();

        include("views/homeAdmin.php");
    }

    public function homeEditor()
    {
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Editor") {
            include("views/inicioSesion.php");
            return;
        }
        $usuario = $_SESSION["usuario"];
        include("views/homeEditor.php");
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Vaciar datos de sesión y destruir
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        // Redirigir a la ruta limpia de login
        header("Location: /login");
        exit;
    }

}
