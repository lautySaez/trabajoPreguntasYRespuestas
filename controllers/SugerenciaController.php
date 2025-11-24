<?php
require_once("models/SugerenciaModel.php");

class SugerenciaController {
    private $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->model = new SugerenciaModel();
    }

    public function mostrarFormulario() {
        if (!isset($_SESSION['usuario']['id'])) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit();
        }
        $categorias = $this->model->obtenerCategoriasActivas();
        include("views/sugerirPregunta.php");
    }

    public function guardar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario']['id'])) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?controller=SugerenciaController&method=mostrarFormulario");
            exit();
        }

        $usuarioId = $_SESSION['usuario']['id'];
        $categoriaId = intval($_POST['categoria_id'] ?? 0);
        $pregunta = trim($_POST['pregunta'] ?? '');
        $r1 = trim($_POST['respuesta_1'] ?? '');
        $r2 = trim($_POST['respuesta_2'] ?? '');
        $r3 = trim($_POST['respuesta_3'] ?? '');
        $r4 = trim($_POST['respuesta_4'] ?? '');
        $correcta = intval($_POST['respuesta_correcta'] ?? 0);

        $errores = [];
        // Validaciones
        if (!$this->model->puedeSugerirHoy($usuarioId)) $errores[] = 'Límite diario alcanzado (2 sugerencias).';
        if (!$this->model->esCategoriaActiva($categoriaId)) $errores[] = 'Categoría inactiva o inexistente.';
        if (strlen($pregunta) < 10 || strlen($pregunta) > 255) $errores[] = 'La pregunta debe tener entre 10 y 255 caracteres.';
        foreach ([$r1,$r2,$r3,$r4] as $resp) {
            if (strlen($resp) < 1 || strlen($resp) > 120) {
                $errores[] = 'Cada respuesta debe tener entre 1 y 120 caracteres.';
                break; // detener tras primer error
            }
        }
        if ($correcta < 1 || $correcta > 4) $errores[] = 'Índice de respuesta correcta inválido.';
        if ($this->model->existePreguntaExacta($pregunta)) $errores[] = 'Ya existe una pregunta igual pendiente o aprobada.';

        $fromModal = isset($_POST['from_modal']) && $_POST['from_modal'] == '1';
        $origin = $_POST['origin_url'] ?? null;
        if ($errores) {
            $_SESSION['flash_error'] = implode(' ', $errores);
            // Persistir datos para repoblar campos
            $_SESSION['sug_data'] = [
                'categoria_id' => $categoriaId,
                'pregunta' => $pregunta,
                'r1' => $r1,
                'r2' => $r2,
                'r3' => $r3,
                'r4' => $r4,
                'correcta' => $correcta
            ];
            if ($fromModal && $origin) {
                header("Location: $origin");
            } else {
                header("Location: index.php?controller=SugerenciaController&method=mostrarFormulario");
            }
            exit();
        }

        try {
            $this->model->crearSugerencia($usuarioId, $categoriaId, $pregunta, $r1, $r2, $r3, $r4, $correcta);
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Error al guardar sugerencia.';
            $_SESSION['sug_data'] = [
                'categoria_id' => $categoriaId,
                'pregunta' => $pregunta,
                'r1' => $r1,
                'r2' => $r2,
                'r3' => $r3,
                'r4' => $r4,
                'correcta' => $correcta
            ];
            if ($fromModal && $origin) {
                header("Location: $origin");
            } else {
                header("Location: index.php?controller=SugerenciaController&method=mostrarFormulario");
            }
            exit();
        }
        echo "<script>alert('Gracias, tu sugerencia quedó pendiente de revisión.'); window.location='index.php?controller=partida&method=mostrarRuleta';</script>";
        exit();
    }

    // ----- AREA EDITOR / ADMIN -----
    private function requiereRolEditor() {
        $rol = $_SESSION['usuario']['rol'] ?? null;
        $rolLower = strtolower($rol ?? '');
        if (!$rol || !in_array($rolLower, ['editor','admin'])) {
            header("Location: index.php?controller=LoginController&method=inicioSesion");
            exit();
        }
    }

    public function pendientes() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->requiereRolEditor();
        $pendientes = $this->model->obtenerPendientes();
        $flashOk = $_SESSION['flash_ok'] ?? null; unset($_SESSION['flash_ok']);
        $flashErr = $_SESSION['flash_error'] ?? null; unset($_SESSION['flash_error']);
        include("views/sugerencias_pendientes.php");
    }

    public function aprobar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->requiereRolEditor();
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0 && $this->model->aprobar($id)) {
            $_SESSION['flash_ok'] = 'Sugerencia aprobada e incorporada.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo aprobar (ya procesada o inexistente).';
        }
        header("Location: index.php?controller=SugerenciaController&method=pendientes");
        exit();
    }

    public function rechazar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->requiereRolEditor();
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0 && $this->model->rechazar($id)) {
            $_SESSION['flash_ok'] = 'Sugerencia rechazada.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo rechazar (ya procesada o inexistente).';
        }
        header("Location: index.php?controller=SugerenciaController&method=pendientes");
        exit();
    }
}
