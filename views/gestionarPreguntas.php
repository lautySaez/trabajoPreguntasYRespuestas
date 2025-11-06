<?php include("views/partials/header.php"); ?>

     <link rel="stylesheet" href="public/css/gestionarPreguntas.css">

<?php
$preguntas = $preguntas ?? [];
$categorias = $categorias ?? [];
$buscar_id = $_GET['buscar_id'] ?? '';
?>

<h1>Gestión de Preguntas</h1>

<div class="filtros-container">
    <form method="get" action="" class="filtro-form">
        <input type="hidden" name="controller" value="editor">
        <input type="hidden" name="method" value="gestionarPreguntas">
        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" id="categoria_id" onchange="this.form.submit()">
            <option value="">Todas las categorías</option>
            <?php foreach($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == ($_GET['categoria_id'] ?? '')) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <form method="get" action="" class="buscar-form">
        <input type="hidden" name="controller" value="editor">
        <input type="hidden" name="method" value="gestionarPreguntas">
        <label for="buscar_id">Buscar por ID:</label>
        <input type="number" name="buscar_id" id="buscar_id" value="<?= htmlspecialchars($buscar_id) ?>" placeholder="Ingrese ID">
        <button type="submit">Buscar</button>
    </form>
</div>

<a href="index.php?controller=editor&method=crearPregunta" class="btn btn-agregar">Agregar Nueva Pregunta</a>

<table class="tabla-preguntas">
    <thead>
    <tr>
        <th>ID</th>
        <th>Pregunta</th>
        <th>Respuestas</th>
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

    if (empty($filtradas)):
        ?>
        <tr>
            <td colspan="5" style="text-align:center;">No se encontraron preguntas.</td>
        </tr>
    <?php else: ?>
        <?php foreach($filtradas as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['pregunta']) ?></td>
                <td>
                    <?php foreach($p['respuestas'] as $r): ?>
                        <?= $r['id'] ?>. <?= htmlspecialchars($r['texto']) ?><br>
                    <?php endforeach; ?>
                </td>
                <td><?= $p['respuesta_correcta'] ?></td>
                <td>
                    <a href="index.php?controller=editor&method=editarPregunta&id=<?= $p['id'] ?>" class="btn-editar">Editar</a> |
                    <a href="index.php?controller=editor&method=borrarPregunta&id=<?= $p['id'] ?>" class="btn-borrar" onclick="return confirm('¿Eliminar esta pregunta?')">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include("views/partials/footer.php"); ?>
