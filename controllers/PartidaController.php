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
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarReglas");
            exit();
        } else {
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarModo");
            exit();
        }
    }

    public function mostrarReglas()
    {
        include("views/reglas.php");
    }

    // Alias cortos para rutas /partida/ruleta, /partida/modo, /partida/reglas
    // Evitan RuntimeException si el usuario accede con el nombre corto.
    public function ruleta()
    {
        $this->mostrarRuleta();
    }
    public function modo()
    {
        $this->mostrarModo();
    }
    public function reglas()
    {
        $this->mostrarReglas();
    }

    public function mostrarRuleta()
    {
        // Bloquear acceso a ruleta si la partida fue finalizada por error o timeout
        if (!empty($_SESSION['partida_finalizada'])) {
            // Forzar al usuario a terminar la partida antes de iniciar otra
            header("Location: /trabajoPreguntasYRespuestas/partida/terminarPartida");
            exit();
        }
        include("views/ruleta.php");
    }

    public function iniciarPartida()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Si la partida está finalizada, no permitir reinicio sin terminar
        if (!empty($_SESSION['partida_finalizada'])) {
            header("Location: /trabajoPreguntasYRespuestas/partida/terminarPartida");
            exit();
        }

        // Evitar cambio de pregunta por refresco mientras hay una pregunta activa sin responder
        if (!empty($_SESSION['preguntas']) && isset($_SESSION['pregunta_actual'])) {
            $preguntas = $_SESSION['preguntas'];
            $indice = $_SESSION['pregunta_actual'];
            $preguntaActual = $preguntas[$indice] ?? null;
            if ($preguntaActual) {
                // Calcular tiempo restante server-side (persistencia tras refresh)
                $usuarioIdTmp = $_SESSION['usuario']['id'] ?? null;
                $tiempoRestante = 10; // default
                if ($usuarioIdTmp && isset($preguntaActual['id'])) {
                    $transcurridos = $this->partidaModel->obtenerSegundosTranscurridos($usuarioIdTmp, $preguntaActual['id']);
                    if ($transcurridos !== null) {
                        $limite = 10; // mismo límite usado en temporizador
                        $restanteCalc = max(0, $limite - $transcurridos);
                        $tiempoRestante = $restanteCalc;
                        if ($tiempoRestante <= 0) {
                            // Fuerza timeout si el refresh ocurre luego del vencimiento
                            $this->tiempoAgotado();
                            return;
                        }
                    }
                }
                include("views/partida.php");
                return;
            }
        }

        if (!isset($_SESSION["usuario"]["id"])) {
            header("Location: /trabajoPreguntasYRespuestas/login");
            exit();
        }

        $categoria = $_GET["categoria"] ?? null;
        if (!$categoria) {
            $_SESSION['flash_error'] = 'Debe elegir una categoría girando la ruleta.';
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarRuleta");
            exit();
        }

        $categoria_id = $this->partidaModel->getCategoriaIdPorNombre($categoria);
        if (!$categoria_id) {
            $_SESSION['flash_error'] = 'Categoría no válida: ' . htmlspecialchars($categoria);
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarRuleta");
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
                header("Location: /trabajoPreguntasYRespuestas/partida/mostrarRuleta");
                exit();
            }
        } else {
            $partidaId = $_SESSION["partida_id"];
        }

        // Selección adaptativa de UNA sola pregunta
        $preguntaSeleccionada = $this->partidaModel->obtenerPreguntaAdaptativaPorCategoriaId($categoria_id, $usuarioId);
        if (!$preguntaSeleccionada) {
            $_SESSION['flash_error'] = 'No quedan preguntas disponibles en esta categoría para vos. Elegí otra categoría.';
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarRuleta");
            exit();
        }
        // Estructura de sesión mantiene formato de array para compatibilidad con responderPregunta
        $_SESSION["preguntas"] = [$preguntaSeleccionada];
        $_SESSION["pregunta_actual"] = 0;
        $_SESSION["categoria_ronda"] = $categoria_id;

        $preguntaActual = $preguntaSeleccionada;
        // Tiempo restante inicial completo al ser nueva entrega
        $tiempoRestante = 10;
        if (isset($preguntaActual['id'])) {
            $this->partidaModel->registrarEntregaPregunta($preguntaActual['id']);
            // Registrar inicio de ventana de tiempo en nueva tabla de tracking
            if (isset($_SESSION['partida_id']) && isset($_SESSION['usuario']['id'])) {
                $this->partidaModel->registrarInicioPreguntaTiempo($_SESSION['partida_id'], $_SESSION['usuario']['id'], $preguntaActual['id']);
            }
        }
        include("views/partida.php");
    }

    public function responderPregunta() {
        $respuestaSeleccionada = $_POST["respuesta"] ?? null;
        $indice = $_SESSION["pregunta_actual"] ?? 0;
        $preguntas = $_SESSION["preguntas"] ?? [];
        $partidaId = $_SESSION["partida_id"] ?? null;
        $usuarioId = $_SESSION['usuario']['id'] ?? null;

        if ($respuestaSeleccionada === null || !$preguntas || !$partidaId) {
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarRuleta");
            exit();
        }

        if ($respuestaSeleccionada === "timeout") {
            $esCorrecta = 0;
        }

        $pregunta = $preguntas[$indice];
        $correcta = $pregunta["respuesta_correcta"];

        // Verificación servidor del tiempo transcurrido (anti-manipulación del temporizador cliente)
        if ($usuarioId && isset($pregunta['id']) && $this->partidaModel->excedioTiempo($usuarioId, $pregunta['id'], 10)) {
            // Tratar como timeout real
            $this->tiempoAgotado();
            return;
        }

        $esCorrecta = ($respuestaSeleccionada == $correcta);
        if ($esCorrecta) {
            $_SESSION["puntaje"] += 2;
            // Fin de ronda por respuesta correcta: limpiar preguntas para permitir nuevo giro
            unset($_SESSION["preguntas"]);
            unset($_SESSION["pregunta_actual"]);
            unset($_SESSION["categoria_ronda"]);
        } else {
            $_SESSION["partida_finalizada"] = true;
            if (isset($pregunta['id'])) {
                $this->partidaModel->registrarIncorrectaPregunta($pregunta['id']);
            }
            // Limpiar preguntas al finalizar por error
            unset($_SESSION["preguntas"]);
            unset($_SESSION["pregunta_actual"]);
            unset($_SESSION["categoria_ronda"]);
        }

        if (isset($_SESSION['usuario']['id']) && isset($pregunta['id'])) {
            $this->partidaModel->registrarPreguntaUsuario($_SESSION['usuario']['id'], $pregunta['id'], $esCorrecta);
            // Cerrar tracking de tiempo
            $this->partidaModel->cerrarPreguntaTiempo($_SESSION['usuario']['id'], $pregunta['id'], $esCorrecta ? 'correcta' : 'incorrecta');
        }

        if (isset($_SESSION['usuario']['id'])) {
            $usuarioId = $_SESSION['usuario']['id'];

            $usuarioModel = new Usuario($this->partidaModel->getConexion());

            $usuarioModel->actualizarEstadisticasJugador($usuarioId, $esCorrecta);
        }

        $this->partidaModel->actualizarPuntaje($partidaId, $_SESSION["puntaje"]);

        // Ya no se avanza a siguiente pregunta en misma ronda.

        $preguntaActual = $pregunta;
        $respuestaSeleccionadaId = (int)$respuestaSeleccionada;
        $respuestaCorrectaId = (int)$correcta;
        include("views/partida_feedback.php");
    }

    // Métodos de ronda continua eliminados (continuarRonda / siguientePregunta)

    public function terminarPartida()
    {
        $puntaje = $_SESSION["puntaje"] ?? 0;
        include("views/resultadoPartida.php");

        unset($_SESSION["preguntas"]);
        unset($_SESSION["pregunta_actual"]);
        unset($_SESSION["partida_id"]);
        unset($_SESSION["puntaje"]);
        unset($_SESSION["partida_finalizada"]);
        unset($_SESSION["tiempo_agotado"]);
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
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarModo");
            exit;
        }

        $pregunta_id = intval($_POST['id_pregunta']);
        $motivo_usuario = trim($_POST['motivo']);

        if ($motivo_usuario === "") {
            $motivo_usuario = "Sin motivo específico.";
        }

        if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
            header("Location: /trabajoPreguntasYRespuestas/login");
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

    public function tiempoAgotado() {
        $indice = $_SESSION["pregunta_actual"] ?? null;
        $preguntas = $_SESSION["preguntas"] ?? [];
        $partidaId = $_SESSION["partida_id"] ?? null;
        if ($indice === null || !$preguntas || !$partidaId) {
            header("Location: /trabajoPreguntasYRespuestas/partida/mostrarRuleta");
            exit();
        }
        $pregunta = $preguntas[$indice];
        $correcta = $pregunta["respuesta_correcta"];
        $_SESSION["puntaje"] = ($_SESSION["puntaje"] ?? 0) - 1;
        $_SESSION["partida_finalizada"] = true;
        $_SESSION["tiempo_agotado"] = true;
        if (isset($_SESSION['usuario']['id']) && isset($pregunta['id'])) {
            $this->partidaModel->registrarIncorrectaPregunta($pregunta['id']);
            $this->partidaModel->registrarPreguntaUsuario($_SESSION['usuario']['id'], $pregunta['id'], false);
            // Cerrar tracking de tiempo con resultado timeout
            $this->partidaModel->cerrarPreguntaTiempo($_SESSION['usuario']['id'], $pregunta['id'], 'timeout');
        }
        if ($partidaId) {
            $this->partidaModel->actualizarPuntaje($partidaId, $_SESSION["puntaje"]);
        }
        unset($_SESSION["preguntas"]);
        unset($_SESSION["pregunta_actual"]);
        unset($_SESSION["categoria_ronda"]);
        $preguntaActual = $pregunta;
        $respuestaCorrectaId = (int)$correcta;
        $respuestaSeleccionadaId = 0; // No respondió
        include("views/partida_feedback.php");
    }

}