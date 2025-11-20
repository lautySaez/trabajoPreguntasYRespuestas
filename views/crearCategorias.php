<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="public/css/crearCategorias.css"> <div class="crear-categoria-page">

    <h1>Crear Nueva Categoría y Preguntas Base</h1>

    <form method="POST" action="index.php?controller=editor&method=crearCategoria" class="form-completo">

        <fieldset class="categoria-data">
            <legend>Datos de la Categoría</legend>

            <label for="nombre">Nombre de la Categoría:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="icono">Icono (Emoji):</label>
            <input type="text" name="icono" id="icono" value="❓" maxlength="5">

            <label for="color">Color:</label>
            <input type="color" name="color" id="color" value="#FFFFFF">

            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="3"></textarea>
        </fieldset>

        <fieldset class="preguntas-data">
            <legend>Preguntas Base (Mínimo 3)</legend>

            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="pregunta-box">
                    <h3>Pregunta <?= $i ?></h3>

                    <label for="pregunta_<?= $i ?>">Pregunta:</label>
                    <input type="text" name="pregunta_<?= $i ?>" required>

                    <label>Respuestas:</label>
                    <input type="text" name="r<?= $i ?>_1" placeholder="Respuesta 1" required>
                    <input type="text" name="r<?= $i ?>_2" placeholder="Respuesta 2" required>
                    <input type="text" name="r<?= $i ?>_3" placeholder="Respuesta 3" required>
                    <input type="text" name="r<?= $i ?>_4" placeholder="Respuesta 4" required>

                    <label for="correcta_<?= $i ?>">Respuesta Correcta (1-4):</label>
                    <input type="number" name="correcta_<?= $i ?>" min="1" max="4" required>
                </div>
                <hr>
            <?php endfor; ?>
        </fieldset>

        <div class="submit-container">
            <button type="submit" class="btn-confirmar">Crear Categoría y Preguntas</button>
            <a href="index.php?controller=editor&method=gestionarCategorias" class="btn-cancelar">Volver</a>
        </div>
    </form>
</div>

    <script src="public/js/crearCategorias.js" defer></script>


<?php include("views/partials/footer.php"); ?>