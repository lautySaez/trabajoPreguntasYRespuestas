<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/homeEditor.css">
    <link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/preguntasReportadasEditor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
$reportes_agrupados = $reportes_agrupados ?? [];
$pregunta_id = $pregunta_id ?? null;
$reportes_detallados = $reportes_detallados ?? [];
$pregunta_info = $pregunta_info ?? null;
$filtro_estado = $_GET['filtro_estado'] ?? 'Activo'; // Obtener el estado actual

require_once("helper/VerificacionDeRoles.php");
verificarRol("Editor");
?>

    <div class="reportes-page">

        <h1><i class="fa-solid fa-triangle-exclamation"></i> Gestión de Preguntas Reportadas</h1>

        <div class="tabs-reportes">
            <a href="/trabajoPreguntasYRespuestas/editor/preguntasReportadas?filtro_estado=Activo"
               class="tab <?= ($filtro_estado === 'Activo' ? 'active' : '') ?>">
                Activos
            </a>
            <a href="/trabajoPreguntasYRespuestas/editor/preguntasReportadas?filtro_estado=Resuelto"
               class="tab <?= ($filtro_estado === 'Resuelto' ? 'active' : '') ?>">
                Resueltos
            </a>
        </div>

        <?php if ($pregunta_id && $pregunta_info): ?>
            <div class="reportes-detalle-container">
                <h2>Detalles de Reportes para la Pregunta ID: <?= $pregunta_id ?></h2>
                <div class="pregunta-card">
                    <p class="pregunta-texto">"<?= htmlspecialchars($pregunta_info['pregunta']) ?>"</p>
                    <div class="pregunta-meta">
                        <span>Categoría: <?= htmlspecialchars($pregunta_info['categoria_nombre'] ?? 'N/A') ?></span>
                        <span class="correcta-tag">Correcta: <?= htmlspecialchars($pregunta_info['respuesta_' . $pregunta_info['respuesta_correcta']]) ?></span>
                    </div>
                </div>

                <div class="acciones-detalle">
                    <a href="/trabajoPreguntasYRespuestas/editor/gestionarPreguntas?filtro_reportes=reportadas&buscar_id=<?= $pregunta_id ?>"
                       class="btn-gestionar">
                        <i class="fa-solid fa-pen-to-square"></i> Revisar Pregunta
                    </a>

                    <?php
                    $hay_activos = array_filter($reportes_detallados, fn($r) => $r['estado'] === 'Activo');
                    if (!empty($hay_activos)): ?>
                        <button class="btn-resuelto" onclick="abrirModalResolucion(<?= $pregunta_id ?>)">
                            <i class="fa-solid fa-check"></i> Marcar como Resuelto
                        </button>
                    <?php endif; ?>

                    <a href="/trabajoPreguntasYRespuestas/editor/preguntasReportadas" class="btn-volver">
                        <i class="fa-solid fa-arrow-left"></i> Volver al Listado
                    </a>
                </div>

                <div class="listado-detalles">
                    <?php if (!empty($reportes_detallados)): ?>
                        <?php foreach ($reportes_detallados as $reporte): ?>
                            <div class="reporte-item <?= ($reporte['estado'] === 'Resuelto' ? 'reporte-resuelto' : '') ?>">
                                <p class="reporte-motivo">Motivo: <strong><?= htmlspecialchars($reporte['motivo']) ?></strong></p>
                                <p class="reporte-meta">
                                    <span class="estado-tag <?= ($reporte['estado'] === 'Activo' ? 'tag-activo' : 'tag-resuelto') ?>">
                                        <?= $reporte['estado'] ?>
                                    </span>
                                    <span class="usuario"><i class="fa-solid fa-user"></i> Reportado por: <?= htmlspecialchars($reporte['nombre_usuario'] ?? 'Usuario Desconocido') ?></span>
                                    <span class="fecha"><i class="fa-solid fa-clock"></i> Fecha: <?= date('d/m/Y H:i', strtotime($reporte['fecha_reporte'])) ?></span>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-resultados">No se encontraron reportes detallados para esta pregunta.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="listado-agrupado-container">
                <p class="instruccion">
                    <?= ($filtro_estado === 'Activo') ? 'Tienes preguntas pendientes de revisión. Utiliza el botón "Resuelto" para limpiarlas.' : 'Estos reportes han sido marcados como revisados y resueltos.' ?>
                </p>

                <?php if (empty($reportes_agrupados)): ?>
                    <p class="no-resultados">
                        <?= ($filtro_estado === 'Activo') ? '🎉 No hay reportes activos actualmente. ¡Todo en orden!' : '📁 No hay reportes en la sección de Resueltos.' ?>
                    </p>
                <?php else: ?>
                    <table class="tabla-reportes">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pregunta</th>
                            <th>Categoría</th>
                            <th>Reportes</th>
                            <th>Último Reporte</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reportes_agrupados as $reporte): ?>
                            <tr class="reporte-row">
                                <td><?= $reporte['pregunta_id'] ?></td>
                                <td class="pregunta-col"><?= htmlspecialchars($reporte['texto_pregunta']) ?></td>
                                <td><?= htmlspecialchars($reporte['categoria_nombre']) ?></td>
                                <td class="reportes-count"><?= $reporte['total_reportes'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($reporte['ultimo_reporte'])) ?></td>
                                <td class="acciones">
                                    <a href="/trabajoPreguntasYRespuestas/editor/preguntasReportadas/<?= $reporte['pregunta_id'] ?>" class="btn-detalles">
                                        <i class="fa-solid fa-eye"></i> Ver Detalles
                                    </a>
                                    <a href="/trabajoPreguntasYRespuestas/editor/gestionarPreguntas?filtro_reportes=reportadas&buscar_id=<?= $reporte['pregunta_id'] ?>"
                                       class="btn-gestionar-pregunta">
                                        <i class="fa-solid fa-pen-to-square"></i> Gestionar
                                    </a>
                                    <?php if ($filtro_estado === 'Activo'): ?>
                                        <button class="btn-resuelto" onclick="abrirModalResolucion(<?= $reporte['pregunta_id'] ?>)">
                                            <i class="fa-solid fa-check"></i> Resuelto
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    <div id="modalResolucion" class="modal">
        <div class="modal-content">
            <h3>Marcar Reporte como Resuelto</h3>
            <p>Esta acción marcará todos los **reportes activos** de esta pregunta como resueltos. Úsala cuando hayas corregido la pregunta o consideres que el reporte es inválido.</p>
            <form id="formResolucion" method="post" action="/trabajoPreguntasYRespuestas/editor/resolverReporte">
                <input type="hidden" name="pregunta_id" id="preguntaIdResolucion">
                <textarea name="motivo_resolucion" placeholder="Motivo de la resolución (Ej: Pregunta corregida, Reporte inválido, etc.)"></textarea>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalResolucion()">Cancelar</button>
                    <button type="submit" class="btn-confirmar">Confirmar Resolución</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalResolucion(preguntaId) {
            document.getElementById('preguntaIdResolucion').value = preguntaId;
            document.getElementById('modalResolucion').style.display = 'flex';
        }

        function cerrarModalResolucion() {
            document.getElementById('modalResolucion').style.display = 'none';
        }
    </script>

<?php include("views/partials/footer.php"); ?>