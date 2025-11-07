<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/gestionarPreguntas.css">
<script src="public/js/gestionarPreguntas.js" defer></script>

<?php
$preguntas = isset($preguntas) ? $preguntas : array();
$categorias = isset($categorias) ? $categorias : array();
$buscar_id = isset($_GET['buscar_id']) ? $_GET['buscar_id'] : '';
?>

<h1>Gestión de Preguntas</h1>

<div class="filtros-container">
    <form method="get" action="" class="filtro-form">
        <input type="hidden" name="controller" value="editor">
        <input type="hidden" name="method" value="gestionarPreguntas">
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" id="categoria_id" onchange="this.form.submit()">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == (isset($_GET['categoria_id']) ? $_GET['categoria_id'] : '')) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <form method="get" action="" class="buscar-form">
        <input type="hidden" name="controller" value="editor">
        <input type="hidden" name="method" value="gestionarPreguntas">
        <label for="buscar_id">Buscar por ID:</label>
        <input type="number" name="buscar_id" id="buscar_id" value="<?php echo htmlspecialchars($buscar_id); ?>" placeholder="Ingrese ID">
        <button type="submit">Buscar</button>
    </form>
</div>

<a href="index.php?controller=editor&method=crearPregunta" class="btn btn-agregar">Agregar Nueva Pregunta</a>

<table class="tabla-preguntas">
    <thead>
    <tr>
        <th>ID</th>
        <th>Pregunta</th>
        <th>Opciones</th>
        <th>Correcta</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $filtradas = $preguntas;
    if ($buscar_id !== '') {
        $filtradas = array_filter($preguntas, function($p) use ($buscar_id) {
            return $p['id'] == $buscar_id;
        });
    }
    ?>

    <?php if (empty($filtradas)): ?>
        <tr><td colspan="5" class="no-resultados">No se encontraron preguntas.</td></tr>
    <?php else: ?>
        <?php foreach ($filtradas as $p): ?>
            <tr id="pregunta-<?php echo $p['id']; ?>">
                <td><?php echo $p['id']; ?></td>
                <td class="editable" data-field="pregunta"><?php echo htmlspecialchars($p['pregunta']); ?></td>
                <td class="editable" data-field="respuestas">
                    <div class="opciones">
                        <div>1. <span data-field="r1"><?php echo htmlspecialchars($p['respuesta_1']); ?></span></div>
                        <div>2. <span data-field="r2"><?php echo htmlspecialchars($p['respuesta_2']); ?></span></div>
                        <div>3. <span data-field="r3"><?php echo htmlspecialchars($p['respuesta_3']); ?></span></div>
                        <div>4. <span data-field="r4"><?php echo htmlspecialchars($p['respuesta_4']); ?></span></div>
                    </div>
                </td>
                <td class="editable" data-field="correcta"><?php echo htmlspecialchars($p['respuesta_correcta']); ?></td>
                <td class="acciones">
                    <button class="btn-editar" onclick="habilitarEdicion(<?php echo $p['id']; ?>)">Editar</button>
                    <button class="btn-borrar" onclick="abrirModal('eliminacion', <?php echo $p['id']; ?>)">Borrar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<!-- Modal de informe -->
<div id="modalAccion" class="modal" style="display:none;">
    <div class="modal-content">
        <h3 id="modalTitulo">Informe de acción</h3>
        <form id="formAccion" method="post" action="">
            <input type="hidden" name="controller" value="editor">
            <input type="hidden" id="tipoAccion" name="tipo_accion" value="">
            <input type="hidden" id="preguntaId" name="id" value="">
            <input type="hidden" id="formData" name="form_data" value="">
            <textarea name="motivo" id="motivo" placeholder="Describa el motivo..." required></textarea>
            <div class="modal-buttons">
                <button type="button" onclick="cerrarModal()" class="btn-cancelar">Cancelar</button>
                <button type="submit" class="btn-confirmar">Aceptar</button>
            </div>
        </form>
    </div>
</div>

<?php include("views/partials/footer.php"); ?>