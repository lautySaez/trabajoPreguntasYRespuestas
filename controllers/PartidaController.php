<?php
require_once("models/PartidaModel.php");

class PartidaController
{
    private $partidaModel;
    private $conexion;

    public function __construct($usuarioModel = null)
    {
        $this->partidaModel = new PartidaModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

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

    public function iniciarPartida()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION["partida_finalizada"])) {
            unset($_SESSION["partida_finalizada"]);
        }

        if (!isset($_SESSION["usuario"]["id"])) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit();
        }

        $categoria = $_GET["categoria"] ?? null;
        if (!$categoria) {
            $_SESSION['flash_error'] = 'Debe elegir una categoría girando la ruleta.';
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }

        $categoria_id = $this->partidaModel->getCategoriaIdPorNombre($categoria);
        if (!$categoria_id) {
            $_SESSION['flash_error'] = 'Categoría no válida: ' . htmlspecialchars($categoria);
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }

        $usuarioId = $_SESSION["usuario"]["id"];

        if (!isset($_SESSION["partida_id"])) {
            try {
                $partidaId = $this->partidaModel->registrarPartida($usuarioId, $categoria_id);
                $_SESSION["partida_id"] = $partidaId;
                $_SESSION["puntaje"] = 0;
            } catch (Exception $e) {
                $_SESSION['flash_error'] = $e->getMessage();
                header("Location: index.php?controller=partida&method=mostrarRuleta");
                exit();
            }
        } else {
            $partidaId = $_SESSION["partida_id"];
        }

        $preguntas = $this->partidaModel->obtenerPreguntasPorCategoriaId($categoria_id, $usuarioId, 1000);
        if (empty($preguntas)) {
            $_SESSION['flash_error'] = 'No quedan preguntas disponibles en esta categoría para vos. Elegí otra categoría.';
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }
        $_SESSION["preguntas"] = $preguntas;
        $_SESSION["pregunta_actual"] = 0;
        $_SESSION["categoria_ronda"] = $categoria_id;

        $preguntaActual = $preguntas[0];
        if (isset($preguntaActual['id'])) {
            $this->partidaModel->registrarEntregaPregunta($preguntaActual['id']);
        }
        include("views/partida.php");
    }

    public function responderPregunta() {
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

        $esCorrecta = ($respuestaSeleccionada == $correcta);
        if ($esCorrecta) {
            $_SESSION["puntaje"] += 2;
            if (isset($_SESSION["partida_finalizada"])) {
                unset($_SESSION["partida_finalizada"]);
            }
        } else {
            $_SESSION["partida_finalizada"] = true;
            if (isset($pregunta['id'])) {
                $this->partidaModel->registrarIncorrectaPregunta($pregunta['id']);
            }
        }

        if (isset($_SESSION['usuario']['id']) && isset($pregunta['id'])) {
            $this->partidaModel->registrarPreguntaUsuario($_SESSION['usuario']['id'], $pregunta['id'], $esCorrecta);
        }

        $this->partidaModel->actualizarPuntaje($partidaId, $_SESSION["puntaje"]);

        if (!$esCorrecta) {
            unset($_SESSION["preguntas"]);
            unset($_SESSION["pregunta_actual"]);
            unset($_SESSION["categoria_ronda"]);
        } else {
            $total = count($preguntas);
            if ($indice + 1 < $total) {
                $_SESSION["pregunta_actual"] = $indice + 1;
            } else {
                unset($_SESSION["preguntas"]);
                unset($_SESSION["pregunta_actual"]);
                unset($_SESSION["categoria_ronda"]);
                $_SESSION['ronda_completada'] = true;
            }
        }

        $preguntaActual = $pregunta;
        $respuestaSeleccionadaId = (int)$respuestaSeleccionada;
        $respuestaCorrectaId = (int)$correcta;
        include("views/partida_feedback.php");
    }

    public function continuarRonda() {
        $preguntas = $_SESSION["preguntas"] ?? [];
        $indice = $_SESSION["pregunta_actual"] ?? null;
        if (!$preguntas || $indice === null) {
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
        }
        $preguntaActual = $preguntas[$indice];
        if (isset($preguntaActual['id'])) {
            $this->partidaModel->registrarEntregaPregunta($preguntaActual['id']);
        }
        include("views/partida.php");
    }

    public function siguientePregunta() {
        $preguntas = $_SESSION["preguntas"] ?? [];
        $indice = $_SESSION["pregunta_actual"] ?? 0;
        $partidaId = $_SESSION["partida_id"] ?? null;

        if (!$preguntas || $partidaId === null) {
            header("Location: index.php?controller=partida&method=mostrarRuleta");
            exit();
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

        unset($_SESSION["preguntas"]);
        unset($_SESSION["pregunta_actual"]);
        unset($_SESSION["partida_id"]);
        unset($_SESSION["puntaje"]);
        unset($_SESSION["partida_finalizada"]);
    }

    public function obtenerCategorias() {
        $stmt = $this->conexion->query("SELECT * FROM categorias ORDER BY nombre");
        return $stmt->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerPreguntasPorCategoria($categoria_id = null) {
        if ($categoria_id) {
            $stmt = $this->conexion->prepare("SELECT * FROM preguntas WHERE categoria_id = ? ORDER BY id DESC");
            $stmt->bind_param("i", $categoria_id);
            $stmt->execute();
            $resultado = $stmt->get_result();
        } else {
            $resultado = $this->conexion->query("SELECT * FROM preguntas ORDER BY id DESC");
        }

        $preguntas = [];
        while ($pregunta = $resultado->fetch_assoc()) {
            $pregunta["respuestas"] = [
                ["id" => 1, "texto" => $pregunta["respuesta_1"]],
                ["id" => 2, "texto" => $pregunta["respuesta_2"]],
                ["id" => 3, "texto" => $pregunta["respuesta_3"]],
                ["id" => 4, "texto" => $pregunta["respuesta_4"]]
            ];
            $preguntas[] = $pregunta;
        }

        return $preguntas;
    }

    public function crearPregunta($categoria_id, $texto, $r1, $r2, $r3, $r4, $correcta) {
        $stmt = $this->conexion->prepare("
        INSERT INTO preguntas (categoria_id, pregunta, respuesta_1, respuesta_2, respuesta_3, respuesta_4, respuesta_correcta)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
        $stmt->bind_param("isssssi", $categoria_id, $texto, $r1, $r2, $r3, $r4, $correcta);
        $stmt->execute();
        return $this->conexion->insert_id;
    }

    public function actualizarPregunta($id, $categoria_id, $texto, $r1, $r2, $r3, $r4, $correcta) {
        $stmt = $this->conexion->prepare("
        UPDATE preguntas
        SET categoria_id=?, pregunta=?, respuesta_1=?, respuesta_2=?, respuesta_3=?, respuesta_4=?, respuesta_correcta=?
        WHERE id=?
    ");
        $stmt->bind_param("isssssii", $categoria_id, $texto, $r1, $r2, $r3, $r4, $correcta, $id);
        $stmt->execute();
    }

    public function borrarPregunta($id) {
        $stmt = $this->conexion->prepare("DELETE FROM preguntas WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    public function reportarPregunta() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_pregunta']) || 
        !isset($_POST['motivo'])) {
            header("Location: index.php?controller=partida&method=mostrarModo");
            exit;
        }

        $pregunta_id = intval($_POST['id_pregunta']);
        $motivo_usuario = trim($_POST['motivo']);

        if ($motivo_usuario === "") {
            $motivo_usuario = "Sin motivo específico.";
        }

        if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit;
        }

        $usuario_id = intval($_SESSION['usuario']['id']);

        require_once("models/Reporte.php");
        $reporteModel = new Reporte();

        try {
            $reporteModel->crearReporte(
                $pregunta_id,
                $usuario_id,
                $motivo_usuario
            );

        } catch (Exception $e) {
            error_log("Error al crear reporte: " . $e->getMessage());
            echo "<script>
                    alert('No se pudo enviar el reporte. Intentá nuevamente.');
                    window.location='index.php?controller=partida&method=terminarPartida';
                </script>";
            exit;
        }

        echo "<script>
                alert('¡Pregunta reportada! La partida se ha finalizado.');
                window.location='index.php?controller=partida&method=terminarPartida';
            </script>";
        exit;
    }

}