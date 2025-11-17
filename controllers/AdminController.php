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
        $kpis = [
            'total_usuarios'    => $this->model->contarUsuarios(),
            'total_preguntas'   => $this->model->contarPreguntas(),
            'total_partidas'    => $this->model->contarPartidas(),
            'usuarios_list'     => $this->model->obtenerUsuarios(200),
        ];

        include(__DIR__ . "/../views/homeAdmin.php");
    }

    public function statsJson() {
        header('Content-Type: application/json; charset=utf-8');

        $payload = [
            'total_preguntas' => $this->model->contarPreguntas(),
            'por_categoria'   => $this->model->preguntasPorCategoria(),
            'top_faciles'     => $this->model->top10PreguntasMasFaciles(),
            'top_jugadores'   => $this->model->mejoresJugadores(10),
            'lugares'         => $this->model->lugaresDondeJuegan(50),
            'edades'          => $this->model->distribucionEdades(),
            'genero'          => $this->model->distribucionGenero(),
            'informes'        => $this->model->obtenerInformes(10),
            'reportes'        => $this->model->obtenerReportesJugadores(10)
        ];

        echo json_encode($payload);
        exit;
    }

    public function informes() {
        $informes = $this->model->obtenerInformes(500);
        include(__DIR__ . "/../views/adminInformes.php");
    }

    public function reportes() {
        $reportes = $this->model->obtenerReportesJugadores(500);
        include(__DIR__ . "/../views/adminReportes.php");
    }

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
                header("Location: index.php?controller=admin&method=reportes");
                exit;
            }
        }
    }
}