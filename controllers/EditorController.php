<?php
require_once("helper/VerificacionDeRoles.php");
require_once("models/editorModel.php");

class EditorController
{
    private $model;

    public function __construct($conexion)
    {
        $this->model = new EditorModel($conexion);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_GET['controller']) && strtolower($_GET['controller']) === 'editor') {
            verificarRol("Editor");
        }
    }

    public function gestionarCategorias()
    {
        $categorias = $this->model->obtenerCategorias();

        include(__DIR__ . "/../views/gestionarCategorias.php");
    } // Muestra la vista de gestión de categorías.
    // Obtiene todas las categorías de la BD y pasa esos datos a la vista para su visualización.

    public function crearCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombre = $_POST['nombre'] ?? '';
            $color = $_POST['color'] ?? '#FFFFFF';
            $icono = $_POST['icono'] ?? '❓';
            $descripcion = $_POST['descripcion'] ?? '';

            if (!empty($nombre)) {
                $categoria_id = $this->model->crearCategoria($nombre, $color, $icono, $descripcion);

                for ($i = 1; $i <= 3; $i++) {
                    if (isset($_POST["pregunta_$i"]) && !empty($_POST["pregunta_$i"])) {
                        $this->model->crearPregunta(
                            $categoria_id,
                            $_POST["pregunta_$i"],
                            $_POST["r{$i}_1"] ?? '',
                            $_POST["r{$i}_2"] ?? '',
                            $_POST["r{$i}_3"] ?? '',
                            $_POST["r{$i}_4"] ?? '',
                            $_POST["correcta_$i"] ?? 1
                        );
                    }
                }

                header("Location: /trabajoPreguntasYRespuestas/editor/gestionarCategorias");
                exit();
            }
        }

        include(__DIR__ . "/../views/crearCategorias.php");
    } // Maneja la creación de una nueva categoría y sus preguntas iniciales.
    // Si es una petición POST, crea la categoría, y si se enviaron preguntas de ejemplo (hasta 3),
    // las crea y las asocia a la nueva categoría.
    // Redirige a la gestión de categorías. Si es GET, muestra el formulario de creación

    public function borrarCategoria()
    {
        $id = $_POST['id'] ?? null;

        if ($id) {
            $this->model->borrarCategoria($id);
        }

        header("Location: /trabajoPreguntasYRespuestas/editor/gestionarCategorias");
        exit();
    } // Elimina una categoría por ID (a través de una petición POST).
    // Llama al modelo para borrarla y redirige a la vista de gestión.

    public function gestionarPreguntas()
    {
        $categoria_id = $_GET["categoria_id"] ?? null;
        $filtro_reportes = $_GET["filtro_reportes"] ?? 'todas';

        $solo_reportadas = null;
        if ($filtro_reportes === 'reportadas') {
            $solo_reportadas = true;
        } elseif ($filtro_reportes === 'no_reportadas') {
            $solo_reportadas = false;
        }

        $categorias = $this->model->obtenerCategorias();
        $preguntas = $this->model->obtenerPreguntasPorCategoria($categoria_id, $solo_reportadas);

        include(__DIR__ . "/../views/gestionarPreguntas.php");
    } // Muestra la vista de gestión de preguntas. Obtiene categorías y preguntas.
    // Permite filtrar las preguntas por categoria_id y por el estado de sus reportes
    // ('reportadas', 'no_reportadas' o 'todas').

    public function editarPregunta()
    {
        if ($_POST) {
            $id = $_POST['id'];
            $motivo = $_POST['motivo'];

            $datosPregunta = json_decode($_POST['form_data'], true);
            $pregunta_vieja = $this->model->obtenerPreguntaPorId($id);

            $this->model->editarPregunta(
                $id,
                $datosPregunta['categoria_id'] ?? $pregunta_vieja['categoria_id'],
                $datosPregunta['pregunta'] ?? $pregunta_vieja['pregunta'],
                $datosPregunta['r1'] ?? $pregunta_vieja['r1'] ?? $pregunta_vieja['respuesta_1'],
                $datosPregunta['r2'] ?? $pregunta_vieja['r2'] ?? $pregunta_vieja['respuesta_2'],
                $datosPregunta['r3'] ?? $datosPregunta['r3'] ?? $pregunta_vieja['respuesta_3'],
                $datosPregunta['r4'] ?? $datosPregunta['r4'] ?? $pregunta_vieja['respuesta_4'],
                $datosPregunta['correcta'] ?? $pregunta_vieja['correcta'] ?? $pregunta_vieja['respuesta_correcta']
            );

            $pregunta_para_informe = [
                'pregunta' => $datosPregunta['pregunta'] ?? $pregunta_vieja['pregunta'],
                'r1' => $datosPregunta['r1'] ?? $pregunta_vieja['r1'] ?? $pregunta_vieja['respuesta_1'],
                'r2' => $datosPregunta['r2'] ?? $pregunta_vieja['r2'] ?? $pregunta_vieja['respuesta_2'],
                'r3' => $datosPregunta['r3'] ?? $pregunta_vieja['r3'] ?? $pregunta_vieja['respuesta_3'],
                'r4' => $datosPregunta['r4'] ?? $pregunta_vieja['r4'] ?? $pregunta_vieja['respuesta_4'],
                'correcta' => $datosPregunta['correcta'] ?? $pregunta_vieja['correcta'] ?? $pregunta_vieja['respuesta_correcta'],
                'categoria_id' => $datosPregunta['categoria_id'] ?? $pregunta_vieja['categoria_id']
            ];

            $this->model->registrarInforme($id, 'Edición', $motivo, $pregunta_para_informe);
            $this->model->marcarReporteComoResuelto($id, "Resuelto al corregir la pregunta: " . $motivo);

            header("Location: /trabajoPreguntasYRespuestas/editor/gestionarPreguntas");
            exit();
        }
    } // Procesa la edición de una pregunta. //
     //1. Actualiza la pregunta en la BD con los nuevos datos recibidos por POST.
    //2. Registra la acción en InformePreguntas con tipo_accion = 'Edición' (con los datos nuevos).
   //3. Marca los reportes de esa pregunta como resueltos.

    public function borrarPregunta()
    {
        $id = $_POST['id'] ?? null;
        $motivo = $_POST['motivo'] ?? null;

        if ($id && $motivo) {
            $pregunta = $this->model->obtenerPreguntaPorId($id);

            $pregunta_para_informe = [
                'pregunta' => $pregunta['pregunta'] ?? '',
                'r1' => $pregunta['r1'] ?? $pregunta['respuesta_1'] ?? '',
                'r2' => $pregunta['r2'] ?? $pregunta['respuesta_2'] ?? '',
                'r3' => $pregunta['r3'] ?? $pregunta['respuesta_3'] ?? '',
                'r4' => $pregunta['r4'] ?? $pregunta['respuesta_4'] ?? '',
                'correcta' => $pregunta['correcta'] ?? $pregunta['respuesta_correcta'] ?? 0,
                'categoria_id' => $pregunta['categoria_id'] ?? 0
            ];

            $this->model->registrarInforme($id, 'Eliminación', $motivo, $pregunta_para_informe);
            $this->model->borrarPregunta($id);

        }

        header("Location: /trabajoPreguntasYRespuestas/editor/gestionarPreguntas");
    } //Procesa la eliminación de una pregunta.
    // 1. Obtiene los datos de la pregunta antes de borrarla.
   // 2. Registra la acción en InformePreguntas con tipo_accion = 'Eliminación'
    // (guardando una copia de la pregunta borrada).
  // 3. Elimina la pregunta de la BD.

    public function preguntasReportadas()
    {

        $filtro_estado = $_GET['filtro_estado'] ?? 'Activo';
        $reportes_agrupados = $this->model->obtenerReportesAgrupados($filtro_estado);
        $pregunta_id = $_GET['id'] ?? null;
        $reportes_detallados = [];
        $pregunta_info = null;

        if ($pregunta_id) {
            $reportes_detallados = $this->model->obtenerReportesDetalladosPorPregunta($pregunta_id);
            $pregunta_info = $this->model->obtenerPreguntaPorId($pregunta_id);
        }

        include(__DIR__ . "/../views/preguntasReportadasEditor.php");
    } // Muestra la vista para revisar las preguntas reportadas.
    //Obtiene la lista de reportes agrupados (por defecto, solo los 'Activos').
    // Si se proporciona un pregunta_id, obtiene también los reportes detallados
    // y la información de la pregunta para mostrarlos

    public function resolverReporte()
    {
        $pregunta_id = $_POST['pregunta_id'] ?? null;
        $motivoIngresado = trim($_POST['motivo_resolucion'] ?? '');
        $motivo = $motivoIngresado !== '' ? $motivoIngresado : 'Marcado resuelto sin motivo.';

        if ($pregunta_id) {
            $this->model->marcarReporteComoResuelto($pregunta_id, $motivo);

            header("Location: /trabajoPreguntasYRespuestas/editor/preguntasReportadas?filtro_estado=Activo");
            exit();
        }
        header("Location: /trabajoPreguntasYRespuestas/editor/preguntasReportadas");
    } // Marca todos los reportes de una pregunta como resueltos.
    // Recibe el pregunta_id y un motivo por POST.
    // Llama al modelo para cambiar el estado de los reportes a 'Resuelto'
    // y, a través de marcarReporteComoResuelto,
    // se registra una acción de auditoría. Redirige a la lista de reportes activos.

    public function crearPregunta()
    {
        $categorias = $this->model->obtenerCategorias();

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
            header("Location: /trabajoPreguntasYRespuestas/editor/gestionarPreguntas");
            exit();
        }

        include(__DIR__ . "/../views/crearPregunta.php");
    } // Maneja la creación de una única pregunta.
    // Si es POST, llama al modelo para insertarla y redirige.
    // Si es GET, muestra el formulario de creación.

    public function index()
    {
        include(__DIR__ . "/../views/homeEditor.php");
    }
}