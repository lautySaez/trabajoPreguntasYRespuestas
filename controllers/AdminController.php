<?php
require_once("helper/VerificacionDeRoles.php");
require_once("models/adminModel.php");

class AdminController {
    public $db;
    private $model;

    public function __construct($conexion) {
        $this->db = $conexion;
        $this->model = new adminModel($conexion);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_GET['controller']) && strtolower($_GET['controller']) === 'admin') {
            verificarRol("Administrador");
        }
    }

    public function homeAdmin() {
        $usuario = $_SESSION['usuario'] ?? null;

        $kpis = [
            'total_usuarios'    => $this->model->contarUsuarios(),
            'total_preguntas'   => $this->model->contarPreguntas(),
            'total_partidas'    => $this->model->contarPartidas(),
            'usuarios_list'     => $this->model->obtenerUsuarios(200),
        ];

        $data = [
            'kpis'    => $kpis,
            'usuario' => $usuario
        ];

        $this->render('homeAdmin', $data);
    } // Muestra la vista de inicio del administrador (Dashboard).
    // Recopila los Key Performance Indicators (KPIs) principales:
    // total de usuarios, total de preguntas y total de partidas.
    // También obtiene una lista reciente de usuarios y pasa toda esta información a la vista.

    public function gestionUsuarios() {
        $usuarios = $this->model->obtenerUsuarios(500);
        $this->render('adminUsuarios', ['usuarios' => $usuarios]);
    } // Muestra la vista completa de gestión de usuarios.
    // Obtiene la lista de usuarios (limitado a 500) y la pasa a la vista para su visualización y gestión.

    public function accionUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $accion = $_POST['accion'] ?? null;
            $current_rol = $_POST['current_rol'] ?? null;

            if ($id && is_numeric($id) && $accion) {
                if ($accion === 'bloquear') {
                    $this->model->actualizarEstadoUsuario($id, 'Bloqueado');
                } elseif ($accion === 'desbloquear') {
                    $this->model->actualizarEstadoUsuario($id, 'Activo');
                } elseif ($accion === 'eliminar') {
                    $this->model->eliminarUsuario($id);
                } elseif ($accion === 'cambiar_rol' && $current_rol) {
                    $nuevoRol = ($current_rol === 'Jugador') ? 'Editor' : 'Jugador';
                    $this->model->actualizarRolUsuario($id, $nuevoRol);
                }

                header("Location: /trabajoPreguntasYRespuestas/admin/gestionUsuarios");
                exit;
            }
        }
        http_response_code(400);
        echo "Acción inválida o datos faltantes.";
        exit;
    } // Procesa las acciones enviadas por POST para modificar un usuario.
    // Recibe un id y una accion (bloquear, desbloquear, eliminar o cambiar_rol).
    // Ejecuta la acción correspondiente a través del modelo y luego redirige a la vista de gestión.

    protected function render($viewName, $data = []) {
        extract($data);

        $viewPath = __DIR__ . "/../views/" . $viewName . ".php";

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "Error: Vista no encontrada.";
        }
    } // Metodo protected de utilidad que se encarga de cargar la vista PHP especificada ($viewName)
    // e inyectarle los datos ($data) para su renderizado.

    public function statsJson() {
        ini_set('display_errors', 0);
        error_reporting(E_ALL);

        if (ob_get_length()) ob_clean();

        header('Content-Type: application/json; charset=utf-8');

        try {
            $payload = [
                'total_preguntas' => $this->model->contarPreguntas() ?? 0,
                'total_partidas'  => $this->model->contarPartidas() ?? 0,
                'por_categoria'   => $this->model->preguntasPorCategoria() ?? [],
                'top_faciles'     => $this->model->top10PreguntasMasFaciles() ?? [],
                'top_jugadores'   => $this->model->mejoresJugadores(10) ?? [],
                'lugares'         => $this->model->lugaresDondeJuegan(500) ?? [],
                'edades'          => $this->model->distribucionEdades() ?? [],
                'genero'          => $this->model->distribucionGenero() ?? [],
                'informes'        => $this->model->obtenerInformes(100) ?? [],
                'reportes'        => $this->model->obtenerReportesJugadores(100) ?? []
            ];

            $json = json_encode($payload);

            if ($json === false) {
                throw new Exception("Error de codificación JSON: " . json_last_error_msg());
            }

            echo $json;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'mensaje' => "Fallo en statsJson: " . $e->getMessage()
            ]);
        }

        exit;
    } // Endpoint de la API que proporciona todas las estadísticas en formato JSON.
    // Recopila todos los datos analíticos del modelo
    // (preguntas por categoría, top jugadores, distribución de edades y género, informes de editor, etc.)
    // y los codifica en una única respuesta JSON.

    public function informes() {
        $informes = $this->model->obtenerInformes(500);
        include(__DIR__ . "/../views/adminInformes.php");
    } // Muestra la vista de auditoría de acciones de editores.
    // Obtiene la lista de registros de la tabla informePreguntas (acciones de edición/eliminación)
    // y la presenta en la vista para su revisión.

    public function reportes() {
        $reportes = $this->model->obtenerReportesJugadores(500);
        include(__DIR__ . "/../views/adminReportes.php");
    } // Muestra la vista de reportes de jugadores.
    // Obtiene la lista de reportes generados por los jugadores y la presenta en la vista.

    public function obtenerDetalleReporte() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de reporte inválido.']);
            exit;
        }

        $id = (int)$_GET['id'];
        header('Content-Type: application/json; charset=utf-8');

        $detalle = $this->model->obtenerDetalleReportePorId($id);

        if ($detalle) {
            echo json_encode($detalle);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Reporte no encontrado.']);
        }
        exit;
    } // Endpoint de la API que devuelve los detalles de un reporte específico.
    // Recibe un id por GET y devuelve la información completa del reporte
    // (incluyendo la pregunta afectada y sus respuestas) en formato JSON,
    // generalmente para mostrar un modal o vista detallada.

    public function accionReporte() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $accion = $_POST['accion'] ?? null;

            if ($id && $accion) {
                if ($accion === 'eliminar') {
                    $stmt = $this->db->prepare("DELETE FROM reportes WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                }
                header("Location: /trabajoPreguntasYRespuestas/admin/reportes");
                exit;
            }
        }
    } // Procesa las acciones sobre los reportes de jugadores.
    // Solo maneja la acción de eliminar un reporte de la tabla reportes por su ID.

}