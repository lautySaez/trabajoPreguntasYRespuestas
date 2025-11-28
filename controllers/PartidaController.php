<?php
require_once(__DIR__ . '/../models/partidaModel.php');

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
            header("Location: /partida/mostrarReglas");
            exit();
        } else {
            header("Location: /partida/mostrarModo");
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
            header("Location: /partida/terminarPartida");
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
            header("Location: /partida/terminarPartida");
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
            header("Location: /login");
            exit();
        }

        $categoria = $_GET["categoria"] ?? null;
        if (!$categoria) {
            $_SESSION['flash_error'] = 'Debe elegir una categoría girando la ruleta.';
            header("Location: /partida/mostrarRuleta");
            exit();
        }

        $categoria_id = $this->partidaModel->getCategoriaIdPorNombre($categoria);
        if (!$categoria_id) {
            $_SESSION['flash_error'] = 'Categoría no válida: ' . htmlspecialchars($categoria);
            header("Location: /partida/mostrarRuleta");
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
                header("Location: /partida/mostrarRuleta");
                exit();
            }
        } else {
            $partidaId = $_SESSION["partida_id"];
        }

        // Selección adaptativa de UNA sola pregunta
        $preguntaSeleccionada = $this->partidaModel->obtenerPreguntaAdaptativaPorCategoriaId($categoria_id, $usuarioId);
        if (!$preguntaSeleccionada) {
            $_SESSION['flash_error'] = 'No quedan preguntas disponibles en esta categoría para vos. Elegí otra categoría.';
            header("Location: /partida/mostrarRuleta");
            exit();
        }
        // Estructura de sesión mantiene formato de array para compatibilidad con responderPregunta
        $_SESSION["preguntas"] = [$preguntaSeleccionada];
        $_SESSION["pregunta_actual"] = 0;
        $_SESSION["categoria_ronda"] = $categoria_id;

       // Convertimos la estructura de BD en el formato usado por la vista
        $preguntaActual = [
            "id" => $preguntaSeleccionada["id"],
            "pregunta" => $preguntaSeleccionada["pregunta"],
            "respuesta_correcta" => (int)$preguntaSeleccionada["respuesta_correcta"],

            // ARMAMOS LAS RESPUESTAS PARA LA VISTA
            "respuestas" => [
                1 => ["texto" => $preguntaSeleccionada["respuesta_1"]],
                2 => ["texto" => $preguntaSeleccionada["respuesta_2"]],
                3 => ["texto" => $preguntaSeleccionada["respuesta_3"]],
                4 => ["texto" => $preguntaSeleccionada["respuesta_4"]],
            ]
        ];

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

    public function responderPregunta()
{
    $respuestaSeleccionada = $_POST["respuesta"] ?? null;
    $indice = $_SESSION["pregunta_actual"] ?? null;
    $preguntas = $_SESSION["preguntas"] ?? [];
    $partidaId = $_SESSION["partida_id"] ?? null;
    $usuarioId = $_SESSION['usuario']['id'] ?? null;

    // Validaciones iniciales
    if ($respuestaSeleccionada === null || $indice === null || empty($preguntas) || !$partidaId) {
        header("Location: /partida/mostrarRuleta");
        exit();
    }

    // Detectar timeout enviado por el front
    $isTimeout = ($respuestaSeleccionada === "timeout");

    $pregunta = $preguntas[$indice];
    $correcta = (int)$pregunta["respuesta_correcta"];

    // Si el usuario seleccionó una opción NO es timeout
    if ($isTimeout) {
        $esCorrecta = false;
    } else {
        $esCorrecta = ((int)$respuestaSeleccionada === $correcta);
    }

    // Actualizar puntaje
    if ($esCorrecta) {
        $_SESSION["puntaje"] = ($_SESSION["puntaje"] ?? 0) + 2;
    } else {
        $_SESSION["puntaje"] = ($_SESSION["puntaje"] ?? 0);
    }

    // Si falló → terminar partida
    if (!$esCorrecta) {
        $_SESSION["partida_finalizada"] = true;
    }

    // Registrar estadísticas por pregunta
    if (isset($pregunta['id'])) {

        // incorrecta
        if (!$esCorrecta) {
            $this->partidaModel->registrarIncorrectaPregunta($pregunta['id']);
        } 
        else { // correcta
            if (method_exists($this->partidaModel, 'registrarCorrectaPregunta')) {
                $this->partidaModel->registrarCorrectaPregunta($pregunta['id']);
            } else {
                $this->partidaModel->recalcularDificultad($pregunta['id']);
            }
        }
    }

    // Registrar historial del jugador
    if ($usuarioId && isset($pregunta['id'])) {

        // Registrar si fue correcta/incorrecta
        $this->partidaModel->registrarPreguntaUsuario($usuarioId, $pregunta['id'], $esCorrecta);

        // Cerrar registro de tiempo con estado correspondiente
        if (method_exists($this->partidaModel, 'cerrarPreguntaTiempo')) {
            $this->partidaModel->cerrarPreguntaTiempo(
                $usuarioId, 
                $pregunta['id'], 
                $esCorrecta ? 'correcta' : ($isTimeout ? 'timeout' : 'incorrecta')
            );
        }
    }

    // Estadísticas generales del usuario
    if ($usuarioId) {
        require_once("models/usuario.php");
        $usuarioModel = new Usuario($this->partidaModel->getConexion());

        if (method_exists($usuarioModel, 'actualizarEstadisticasJugador')) {
            $usuarioModel->actualizarEstadisticasJugador($usuarioId, $esCorrecta);
        }
    }

    // Actualizar puntaje de la partida en BD
    if ($partidaId) {
        $this->partidaModel->actualizarPuntaje($partidaId, $_SESSION["puntaje"]);
    }

    // Limpiar sesión para siguiente pregunta
    unset($_SESSION["preguntas"]);
    unset($_SESSION["pregunta_actual"]);
    unset($_SESSION["categoria_ronda"]);

    // Preparar feedback
    $preguntaActual = $pregunta;
    $respuestaSeleccionadaId = ($isTimeout ? 0 : (int)$respuestaSeleccionada);
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
            header("Location: /partida/mostrarModo");
            exit;
        }

        $pregunta_id = intval($_POST['id_pregunta']);
        $motivo_usuario = trim($_POST['motivo']);

        if ($motivo_usuario === "") {
            $motivo_usuario = "Sin motivo específico.";
        }

        if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
            header("Location: /login");
            exit;
        }

        $usuario_id = intval($_SESSION['usuario']['id']);

        require_once("models/reporte.php");
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
            header("Location: /partida/mostrarRuleta");
            exit();
        }
        $pregunta = $preguntas[$indice];
        $correcta = $pregunta["respuesta_correcta"];
        $_SESSION["puntaje"] = ($_SESSION["puntaje"] ?? 0);
        $_SESSION["partida_finalizada"] = true;
        $_SESSION["tiempo_agotado"] = true;

        if (isset($_SESSION['usuario']['id']) && isset($pregunta['id'])) {
            $usuarioId = $_SESSION['usuario']['id'];

            $this->partidaModel->registrarIncorrectaPregunta($pregunta['id']);
            $this->partidaModel->registrarPreguntaUsuario($usuarioId, $pregunta['id'], false);
            // Cerrar tracking de tiempo con resultado timeout
            if (method_exists($this->partidaModel, 'cerrarPreguntaTiempo')) {
                $this->partidaModel->cerrarPreguntaTiempo($usuarioId, $pregunta['id'], 'timeout');
            }

            require_once("models/usuario.php");
            $usuarioModel = new Usuario($this->partidaModel->getConexion());
            if (method_exists($usuarioModel, 'actualizarEstadisticasJugador')) {
                $usuarioModel->actualizarEstadisticasJugador($usuarioId, false);
            }
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