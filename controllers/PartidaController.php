<?php
require_once("models/PartidaModel.php");

class PartidaController
{
    private $partidaModel;

    public function __construct($usuarioModel = null)
    {
        $this->partidaModel = new PartidaModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Metodos previo a iniciar partida
    public function mostrarModo()
    {
        include("views/modoDeJuego.php");
    }

    public function guardarModo()
    {
        if (isset($_POST["modo"])) {
            $_SESSION["modo_juego"] = $_POST["modo"];
            header("Location: index.php?controller=partida&method=mostrarReglas");
            exit();
        } else {
            header("Location: index.php?controller=partida&method=mostrarModo");
            exit();
        }
    }

    public function mostrarReglas()
    {
        include("views/reglas.php");
    }

    public function mostrarRuleta()
    {
        include("views/ruleta.php");
    }

    // Metodos de la partida
    public function iniciarPartida()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar que el usuario esté logueado
        if (!isset($_SESSION["usuario"]["id"])) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit();
        }

        // Obtener categoría desde la ruleta
        $categoria = $_GET["categoria"] ?? null;
        if (!$categoria) {
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }

        // Obtener el ID de la categoría desde la tabla categorias
        $categoria_id = $this->partidaModel->getCategoriaIdPorNombre($categoria);
        if (!$categoria_id) {
            // Si la categoría no existe, volvemos a la ruleta
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }

        // Registrar la partida con el ID correcto
        $usuarioId = $_SESSION["usuario"]["id"];
        $partidaId = $this->partidaModel->registrarPartida($usuarioId, $categoria_id);
        $_SESSION["partida_id"] = $partidaId;
        $_SESSION["puntaje"] = 0;

        // Obtener 4 preguntas aleatorias para la categoría
        $preguntas = $this->partidaModel->obtenerPreguntasPorCategoriaId($categoria_id);
        if (empty($preguntas)) {
            // No hay preguntas disponibles
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }
        $_SESSION["preguntas"] = $preguntas;
        $_SESSION["pregunta_actual"] = 0;

        // Cargar la primera pregunta
        $preguntaActual = $preguntas[0];
        include("views/partida.php");
    }

    public function responderPregunta()
    {
        $respuestaSeleccionada = $_POST["respuesta"] ?? null;
        $indice = $_SESSION["pregunta_actual"] ?? 0;
        $preguntas = $_SESSION["preguntas"] ?? [];
        $partidaId = $_SESSION["partida_id"] ?? null;

        if ($respuestaSeleccionada === null || !$preguntas || !$partidaId) {
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }

        $pregunta = $preguntas[$indice];
        $correcta = $pregunta["respuesta_correcta"];

        if ($respuestaSeleccionada == $correcta) {
            $_SESSION["puntaje"] += 2;
        } else {
            $_SESSION["puntaje"] -= 1;
        }

        $_SESSION["pregunta_actual"]++;

        if ($_SESSION["pregunta_actual"] >= count($preguntas)) {
            // Fin de partida
            $this->partidaModel->actualizarPuntaje($partidaId, $_SESSION["puntaje"]);
            header("Location: index.php?controller=partida&method=terminarPartida");
            exit();
        } else {
            $preguntaActual = $preguntas[$_SESSION["pregunta_actual"]];
            include("views/partida.php");
        }
    }

    public function terminarPartida()
    {
        $puntaje = $_SESSION["puntaje"] ?? 0;
        include("views/resultadoPartida.php");

        // Limpiar sesión de la partida
        unset($_SESSION["preguntas"]);
        unset($_SESSION["pregunta_actual"]);
        unset($_SESSION["partida_id"]);
        unset($_SESSION["puntaje"]);
    }
}