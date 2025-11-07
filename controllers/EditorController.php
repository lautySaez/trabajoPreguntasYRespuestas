<?php
require_once("helper/VerificacionDeRoles.php");
require_once("models/EditorModel.php");

class EditorController {
    private $model;

    public function __construct($conexion) {
        $this->model = new EditorModel($conexion);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_GET['controller']) && strtolower($_GET['controller']) === 'editor') {
            verificarRol("Editor");
        }
    }

    public function gestionarPreguntas() {
        $categoria_id = $_GET["categoria_id"] ?? null;

        $categorias = $this->model->obtenerCategorias();
        $preguntas = $this->model->obtenerPreguntasPorCategoria($categoria_id);

        $categorias = $categorias ?? [];
        $preguntas = $preguntas ?? [];

        include(__DIR__ . "/../views/gestionarPreguntas.php");
    }

    public function crearPregunta() {
        if ($_POST) {
            $this->model->crearPregunta(
                $_POST["categoria_id"],
                $_POST["pregunta"],
                $_POST["r1"],
                $_POST["r2"],
                $_POST["r3"],
                $_POST["r4"],
                $_POST["correcta"]
            );
            header("Location: index.php?controller=editor&method=gestionarPreguntas");
            exit();
        }

        $categorias = $this->model->obtenerCategorias();
        include(__DIR__ . "/../views/crearPregunta.php");
    }

 /*   public function editarPregunta() {
        if ($_POST) {
            $id = $_POST['id'];
            $motivo = $_POST['motivo'];

            $datosPregunta = json_decode($_POST['form_data'], true);

            $pregunta_vieja = $this->model->obtenerPreguntaPorId($id);

            $this->model->editarPregunta(
                $id,
                $datosPregunta['categoria_id'] ?? $pregunta_vieja['categoria_id'],
                $datosPregunta['pregunta'] ?? $pregunta_vieja['pregunta'],
                $datosPregunta['r1'] ?? $pregunta_vieja['respuesta_1'],
                $datosPregunta['r2'] ?? $pregunta_vieja['respuesta_2'],
                $datosPregunta['r3'] ?? $pregunta_vieja['respuesta_3'],
                $datosPregunta['r4'] ?? $pregunta_vieja['respuesta_4'],
                $datosPregunta['correcta'] ?? $pregunta_vieja['respuesta_correcta']
            );

            $this->model->registrarInforme($id, 'edicion', $motivo, $pregunta_vieja);

            header("Location: index.php?controller=editor&method=gestionarPreguntas");
            exit();
        }
    }

    public function borrarPregunta() {
        $id = $_POST['id'] ?? null;
        $motivo = $_POST['motivo'] ?? null;
        if ($id && $motivo) {
            $pregunta = $this->model->obtenerPreguntaPorId($id);
            $this->model->registrarInforme($id, 'eliminacion', $motivo, $pregunta);
            $this->model->borrarPregunta($id);
        }
        header("Location: index.php?controller=editor&method=gestionarPreguntas");
    } */
}