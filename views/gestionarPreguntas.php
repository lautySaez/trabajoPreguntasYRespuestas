<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/gestionarPreguntas.css">

<?php
$preguntas = $preguntas ?? [];
$categorias = $categorias ?? [];
$buscar_id = $_GET['buscar_id'] ?? '';
$categoria_id = $_GET['categoria_id'] ?? '';
?>

<div class="gestionar-preguntas-page">

    <h1>Gestión de Preguntas</h1>

    <div class="filtros-container">

        <form method="get" class="filtro-form">
            <input type="hidden" name="controller" value="editor">
            <input type="hidden" name="method" value="gestionarPreguntas">

            <label for="categoria_id">Categoría:</label>
            <select name="categoria_id" id="categoria_id" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $categoria_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <form method="get" class="buscar-form">
            <input type="hidden" name="controller" value="editor">
            <input type="hidden" name="method" value="gestionarPreguntas">

            <label for="buscar_id">Buscar por ID:</label>
            <input type="number" name="buscar_id" id="buscar_id" value="<?= htmlspecialchars($buscar_id) ?>" placeholder="Ingrese ID">
            <button type="submit">Buscar</button>
        </form>

    </div>

    <a href="index.php?controller=editor&method=crearPregunta" class="btn-agregar">Agregar Nueva Pregunta</a>

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
            $filtradas = array_filter($preguntas, fn($p) => $p['id'] == $buscar_id);
        }
        ?>
        <?php if (empty($filtradas)): ?>
            <tr><td colspan="5" class="no-resultados">No se encontraron preguntas.</td></tr>
        <?php else: ?>
            <?php foreach ($filtradas as $p): ?>
                <tr id="pregunta-<?= $p['id'] ?>" data-id="<?= $p['id'] ?>">
                    <td><?= $p['id'] ?></td>
                    <td class="editable" data-field="pregunta"><?= htmlspecialchars($p['pregunta']) ?></td>
                    <td class="editable" data-field="respuestas">
                        <div class="opciones">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div><?= $i ?>. <span data-field="r<?= $i ?>"><?= htmlspecialchars($p["respuesta_$i"]) ?></span></div>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td class="editable" data-field="correcta"><?= htmlspecialchars($p['respuesta_correcta']) ?></td>
                    <td class="acciones">
                        <button class="btn-editar" onclick="habilitarEdicion(<?= $p['id'] ?>)">Editar</button>
                        <button class="btn-borrar" onclick="abrirModal('eliminacion', <?= $p['id'] ?>)">Borrar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div id="modalAccion" class="modal">
        <div class="modal-content">
            <h3 id="modalTitulo">Informe de acción</h3>
            <form id="formAccion" method="post">
                <input type="hidden" name="controller" value="editor">
                <input type="hidden" id="tipoAccion" name="tipo_accion">
                <input type="hidden" id="preguntaId" name="id">
                <input type="hidden" id="formData" name="form_data">
                <textarea name="motivo" id="motivo" placeholder="Describa el motivo..." required></textarea>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-confirmar">Aceptar</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="public/js/gestionarPreguntas.js" defer></script>

<?php include("views/partials/footer.php"); ?>
