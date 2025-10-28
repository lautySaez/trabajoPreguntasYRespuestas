<?php
// Intentar cargar PHPMailer desde diferentes ubicaciones
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
    // Fallback: cargar PHPMailer directamente
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

            // Validaciones
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

            // Si no hay errores, procesamos el registro
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
                    $mail = new PHPMailer(true);


                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'aciertayaa@gmail.com'; // Tu correo Gmail
                    $mail->Password   = 'egnq wplg anyu plah'; // Contraseña o App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Remitente y destinatario
                    $mail->setFrom('aciertayaa@gmail.com', 'AciertaYa');
                    $mail->addAddress($email, 'Destinatario');

                    // Contenido
                    $mail->isHTML(true);
                    $mail->Subject = 'Bienvenido a AciertaYaa';
                    $mail->Body    = '<h1>Bienvenido a AciertaYaa!</h1><p>por favor ingrese el codigo que se enviamos para confirmar el registro: <strong>' . $token_activacion . '</strong></p>';

                    $mail->send();

                    $mensaje = "Usuario registrado correctamente. Ahora puedes iniciar sesión.";
                    header("Location: index.php?controller=login&method=inicioSesion");
                    exit();
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
                    // Redirigir al método elegirAvatar con el nombre de usuario
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
            // Si no hay usuario logueado, redirigir al login
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit();
        }

        // Pasamos los datos del usuario a la vista
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

                // Actualizamos la sesión para reflejar el nuevo avatar
                $_SESSION['usuario']['foto_perfil'] = $foto_perfil;
            }

            // Redirigir al perfil actualizado
            header("Location: index.php?controller=LoginController&method=perfil");
            exit();
        }
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
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Administrador") {
            include("views/inicioSesion.php");
            return;
        }
        $usuario = $_SESSION["usuario"];
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

    public function perfil()
    {
        $usuario = $_SESSION['usuario'];
        include("views/perfil.php");
    }

    public function actualizarPerfil()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioSesion = $_SESSION['usuario'] ?? null;
            if (!$usuarioSesion) {
                header("Location: index.php?controller=LoginController&method=inicioSesion");
                exit;
            }

            $id = $_POST['id'] ?? $usuarioSesion['id'];

            $nombre_usuario = trim($_POST['nombre_usuario']);
            $email = trim($_POST['email']);
            $password = $_POST['password'] ?? null;
            $repassword = $_POST['repassword'] ?? null;

            $foto_perfil = $_FILES['foto_perfil']['name'] ?? null;
            $ruta_avatar = null;

            if ($foto_perfil) {
                $uploadDir = "uploads/";
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
                $ruta_avatar = $uploadDir . basename($foto_perfil);
                move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_avatar);
            }

            if ($password && $password !== $repassword) {
                $_SESSION['error'] = "Las contraseñas no coinciden.";
                header("Location: index.php?controller=LoginController&method=perfil");
                exit;
            }

            $this->usuarioModel->actualizarPerfil(
                $id,
                $nombre_usuario,
                $email,
                $password,
                $ruta_avatar
            );

            $_SESSION['usuario'] = $this->usuarioModel->obtenerPorId($id);

            header("Location: index.php?controller=LoginController&method=home");
            exit;
        }
    }

    public function logout()
    {
        session_destroy();
        include("views/inicioSesion.php");
    }
}
