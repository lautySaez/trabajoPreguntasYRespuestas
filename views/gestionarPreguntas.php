<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="/public/css/homeEditor.css">
    <link rel="stylesheet" href="/public/css/gestionarPreguntas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
$preguntas = $preguntas ?? [];
$categorias = $categorias ?? [];
$buscar_id = $_GET['buscar_id'] ?? '';
$categoria_id = $_GET['categoria_id'] ?? '';
$filtro_reportes = $_GET['filtro_reportes'] ?? 'todas';

require_once("helper/VerificacionDeRoles.php");
verificarRol("Editor");
?>

    <div class="gestionar-preguntas-page">

        <h1>Gestión de Preguntas</h1>

        <div class="filtros-container filtros-linea">
            <form method="get" class="filtro-form filtro-inline">
                <input type="hidden" name="controller" value="editor">
                <input type="hidden" name="method" value="gestionarPreguntas">
                <input type="hidden" name="filtro_reportes" value="<?= htmlspecialchars($filtro_reportes) ?>"> <input type="hidden" name="buscar_id" value="<?= htmlspecialchars($buscar_id) ?>">

                <label for="categoria_id">Categoría</label>
                <select name="categoria_id" id="categoria_id" onchange="this.form.submit()" class="input-inline">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $categoria_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <form method="get" class="filtro-reportes-form filtro-inline">
                <input type="hidden" name="controller" value="editor">
                <input type="hidden" name="method" value="gestionarPreguntas">
                <input type="hidden" name="categoria_id" value="<?= htmlspecialchars($categoria_id) ?>"> <input type="hidden" name="buscar_id" value="<?= htmlspecialchars($buscar_id) ?>">

                <label for="filtro_reportes">Estado</label>
                <select name="filtro_reportes" id="filtro_reportes" onchange="this.form.submit()" class="input-inline">
                    <option value="todas" <?= ($filtro_reportes === 'todas') ? 'selected' : '' ?>>Todas las preguntas</option>
                    <option value="reportadas" <?= ($filtro_reportes === 'reportadas') ? 'selected' : '' ?>>Reportadas</option>
                    <option value="no_reportadas" <?= ($filtro_reportes === 'no_reportadas') ? 'selected' : '' ?>>No Reportadas</option>
                </select>
            </form>

            <form method="get" class="buscar-form filtro-inline">
                <input type="hidden" name="controller" value="editor">
                <input type="hidden" name="method" value="gestionarPreguntas">
                <input type="hidden" name="filtro_reportes" value="<?= htmlspecialchars($filtro_reportes) ?>">
                <input type="hidden" name="categoria_id" value="<?= htmlspecialchars($categoria_id) ?>">

                <label for="buscar_id">ID</label>
                <input type="number" name="buscar_id" id="buscar_id" value="<?= htmlspecialchars($buscar_id) ?>" placeholder="Ingrese ID" class="input-inline">
                <!--<button type="submit" class="btn-inline">Buscar</button>-->
            </form>

        </div>

        <a href="/editor/crearPregunta" class="btn-agregar">Agregar Nueva Pregunta</a>

        <div class="tabla-container">
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
                if ($buscar_id !== '' && $filtro_reportes === 'todas' && empty($categoria_id)) {
                    $filtradas = array_filter($preguntas, function($p) use ($buscar_id) {
                        return $p['id'] == $buscar_id;
                    });
                } else if ($buscar_id !== '') {
                    $filtradas = array_filter($preguntas, function($p) use ($buscar_id) {
                        return $p['id'] == $buscar_id;
                    });
                }
                ?>
                <?php if (empty($filtradas)): ?>
                    <tr><td colspan="5" class="no-resultados">No se encontraron preguntas que coincidan con los filtros.</td></tr>
                <?php else: ?>
                    <?php foreach ($filtradas as $p):
                        $esReportada = $p['reportes_count'] > 0;
                        $claseReporte = $esReportada ? 'fila-reportada' : '';
                        $reporteIcono = $esReportada ? '<i class="fa-solid fa-triangle-exclamation reporte-icono" title="Reportada: ' . $p['reportes_count'] . ' veces"></i>' : '';
                        ?>
                        <tr id="pregunta-<?= $p['id'] ?>" data-id="<?= $p['id'] ?>" class="<?= $claseReporte ?>">
                            <td><?= $p['id'] ?></td>
                            <td class="editable" data-field="pregunta"><?= $reporteIcono ?> <?= htmlspecialchars($p['pregunta']) ?></td>
                            <td class="editable" data-field="respuestas">
                                <div class="opciones">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div><?= $i ?>. <span data-field="r<?= $i ?>"><?= htmlspecialchars($p["respuesta_$i"]) ?></span></div>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td class="editable" data-field="correcta"><?= htmlspecialchars($p['respuesta_correcta']) ?></td>
                            <td class="acciones">
                                <?php if ($esReportada): ?>
                                    <a href="editor/preguntasReportadas?id=<?= $p['id'] ?>" class="btn-reporte-info">
                                        Reporte (<?= $p['reportes_count'] ?>)
                                    </a>
                                <?php endif; ?>
                                <button class="btn-editar" onclick="habilitarEdicion(<?= $p['id'] ?>)">Editar</button>
                                <button class="btn-borrar" onclick="abrirModal('eliminacion', <?= $p['id'] ?>)">Borrar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

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

    <script src="/public/js/gestionarPreguntas.js" defer></script>

<?php include("views/partials/footer.php"); ?>