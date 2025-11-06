<?php
require_once("helper/VerificacionDeRoles.php");
require_once("models/EditorModel.php");

class EditorController {
    private $model;

    public function __construct($conexion) {
        $this->model = new EditorModel($conexion);

        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        verificarRol("Editor");
    }

    public function gestionarPreguntas() {
        $categoria_id = $_GET["categoria_id"] ?? null;

        $categorias = $this->model->obtenerCategorias();
        $preguntas = $this->model->obtenerPreguntasPorCategoria($categoria_id);

        // Inicializar variables para evitar warnings
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

    public function editarPregunta() {
        $id = $_GET["id"] ?? null;
        if (!$id) {
            header("Location: index.php?controller=editor&method=gestionarPreguntas");
            exit();
        }

        if ($_POST) {
            $this->model->editarPregunta(
                $id,
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

        $pregunta = $this->model->obtenerPreguntaPorId($id);
        if (!$pregunta) {
            header("Location: index.php?controller=editor&method=gestionarPreguntas");
            exit();
        }

        include(__DIR__ . "/../views/editarPregunta.php");
    }

    public function borrarPregunta() {
        $id = $_GET["id"] ?? null;
        if ($id) {
            $this->model->borrarPregunta($id);
        }
        header("Location: index.php?controller=editor&method=gestionarPreguntas");
    }
}