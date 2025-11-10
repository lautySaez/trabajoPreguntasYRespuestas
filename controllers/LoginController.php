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

    public function __construct($usuarioModel)
    {
        require_once("models/PartidaModel.php");
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

            switch ($rol) {
                case "Administrador":
                    include("views/homeAdmin.php");
                    return;
                case "Editor":
                    include("views/homeEditor.php");
                    return;
                case "Jugador":
                default:
                    include("views/home.php");
                    return;
            }
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
                    $foto_perfil,
                    $estado_registro,
                    $token_activacion
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
                            $mail->Body    = '<h1>Bienvenido a AciertaYaa!</h1><p>Por favor, ingresá el siguiente código para confirmar tu registro: <strong>' . $token_activacion . '</strong></p>';

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
                    header("Location: index.php?controller=LoginController&method=elegirAvatar&usuario=" . urlencode($nombre_usuario));
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
            header("Location: index.php?controller=LoginController&method=inicioSesion");
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
                header("Location: index.php?controller=LoginController&method=inicioSesion");
                exit();
            }

            $nombre_usuario = $usuario['nombre_usuario'];
            $foto_perfil = $_POST['foto_perfil'] ?? null;

            if ($foto_perfil) {
                $this->usuarioModel->actualizarAvatar($nombre_usuario, $foto_perfil);

                $_SESSION['usuario']['foto_perfil'] = $foto_perfil;
            }

            header("Location: index.php?controller=LoginController&method=home");
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

        // Normalizar rol a minúsculas
        $rol = strtolower($usuario['rol'] ?? '');

        // Si es jugador, obtener su última partida
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

        require_once("models/usuario.php");
        require_once("helper/MyConexion.php");
        $conexion = new MyConexion("localhost", "root", "", "preguntas_respuestas");
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
        session_destroy();
        include("views/inicioSesion.php");
    }

    public function iniciarNuevaPartida()
    {

    }
}
