<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="public/css/homeEditor.css">
    <link rel="stylesheet" href="public/css/gestionarCategorias.css">

<?php
$categorias = $categorias ?? [];

require_once("helper/VerificacionDeRoles.php");
verificarRol("Editor");
?>

    <div class="gestionar-categorias-page">

        <h1>Gestión de Categorías</h1>

        <a href="index.php?controller=editor&method=crearCategoria" class="btn-agregar-categoria">
            ➕ Crear Nueva Categoría
        </a>

        <div class="listado-categorias">
            <h2>📋 Listado de Categorías Existentes</h2>
            <?php if (empty($categorias)): ?>
                <p class="no-resultados">No hay categorías creadas.</p>
            <?php else: ?>
                <table class="tabla-categorias">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Icono</th>
                        <th>Color</th>
                        <th>Preguntas</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categorias as $cat):
                        // Nota: La lógica de $this->model->contarPreguntasPorCategoria() debe estar disponible
                        // Si no lo está, esta línea dará un error. Asumimos que $preguntas_count se está calculando.
                        $preguntas_count = $this->model->contarPreguntasPorCategoria($cat['id']) ?? 0;
                        ?>
                        <tr data-id="<?= $cat['id'] ?>" data-nombre="<?= htmlspecialchars($cat['nombre']) ?>" data-count="<?= $preguntas_count ?>">
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['nombre']) ?></td>
                            <td><?= htmlspecialchars($cat['icono']) ?></td>
                            <td style="background-color: <?= htmlspecialchars($cat['color']) ?>;"><?= htmlspecialchars($cat['color']) ?></td>
                            <td><?= $preguntas_count ?></td>
                            <td class="acciones">
                                <button class="btn-borrar"
                                        onclick="abrirModalCategoria('<?= $cat['id'] ?>', '<?= htmlspecialchars($cat['nombre']) ?>', '<?= $preguntas_count ?>')">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

    <div id="modalEliminacionCategoria" class="modal">
        <div class="modal-content">
            <h3>⚠️ Advertencia: Eliminación de Categoría</h3>
            <p>¿Está seguro de que desea eliminar la categoría: <strong id="nombreCategoriaModal"></strong>?</p>
            <p class="warning-text">Esta acción **eliminará permanentemente** <strong id="preguntasAfectadasModal"></strong> asociadas.</p>

            <form id="formBorrarCategoria" method="post" action="index.php?controller=editor&method=borrarCategoria">
                <input type="hidden" name="id" id="categoriaIdModal">

                <div class="modal-buttons">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalCategoria()">Cancelar</button>
                    <button type="submit" class="btn-confirmar-borrar">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>


    <script src="public/js/gestionarCategorias.js" defer></script>

<?php include("views/partials/footer.php"); ?>