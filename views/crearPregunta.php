<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/crearPregunta.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php
$categorias = $categorias ?? [];
?>

<div class="crear-pregunta-page">
    <h1>Agregar Nueva Pregunta</h1>

    <form method="post" action="index.php?controller=editor&method=crearPregunta" class="form-crear-pregunta">

        <label for="categoria_id">Categoría:</label>
        <select name="categoria_id" id="categoria_id" required>
            <option value="">Seleccione categoría</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="pregunta">Pregunta:</label>
        <textarea name="pregunta" id="pregunta" required placeholder="Escriba la pregunta aquí"></textarea>

        <label>Respuestas (marque la correcta):</label>
        <div class="respuestas">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="respuesta-item">
                    <input type="text" name="r<?= $i ?>" placeholder="Respuesta <?= $i ?>" required>
                    <label class="check-container">
                        <input type="radio" name="correcta" value="<?= $i ?>" required>
                        <span class="checkmark"></span>
                    </label>
                </div>
            <?php endfor; ?>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-guardar">Guardar Pregunta</button>
            <a href="index.php?controller=editor&method=gestionarPreguntas" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<?php include("views/partials/footer.php"); ?>