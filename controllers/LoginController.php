<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

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
                    include("views/validarRegistroUsuario.php");
                    return;
                } else {
                    $error = "El usuario o el email ya existen.";
                }
            }

            include("views/registro.php");
            return;
        }

        // Si no es POST, solo mostramos el registro vacío
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
                    $mensaje = "Registro validado correctamente. Ahora puedes iniciar sesión.";
                    switch ($validarUsuario["rol"]) {
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
                    include("views/validarRegistroUsuario.php");
                    return;
                } else {
                    $error = "El usuario o el email ya existen.";
                }
            }
            include("views/validarRegistroUsuario.php");
            return;
        }
        include("views/validarRegistroUsuario.php");
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
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Administrador") {
            include("views/inicioSesion.php");
            return;
        }
        include("views/homeAdmin.php");
    }

    public function homeEditor()
    {
        if (!isset($_SESSION["usuario"]) || $_SESSION["usuario"]["rol"] !== "Editor") {
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
