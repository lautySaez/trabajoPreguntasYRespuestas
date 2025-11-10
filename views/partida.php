<?php include("views/partials/header.php"); ?>

<div class="partida-contenedor">

 <?php if (!empty($preguntaActual) && !empty($preguntaActual["respuestas"])): ?>
    <h2><?= htmlspecialchars($preguntaActual["pregunta"]) ?></h2>

    <div class="respuestas-grid">
        <?php foreach ($preguntaActual["respuestas"] as $respuesta): ?>
            <form method="POST" action="index.php?controller=partida&method=responderPregunta" style="display:inline;">
                <input type="hidden" name="respuesta" value="<?= $respuesta["id"] ?>">
                <button type="submit"><?= htmlspecialchars($respuesta["texto"]) ?></button>
            </form>
        <?php endforeach; ?>
    </div>

    <div id="temporizador" class="badge bg-danger fs-5 mt-2">
    </div>

        <!-- boton reportar
        <form method="POST" action="index.php?controller=partida&method=reportarPregunta" style="position: absolute; bottom: 0; left: 0;">
            <input type="hidden" name="pregunta_id" value="<?= $preguntaActual["id"] ?>">
            <button type="submit" class="boton-reportar">
                Reportar
            </button>
        </form>
    </div> -->

<?php else: ?>
    <p>No hay preguntas disponibles para esta categoría.</p>
<?php endif; ?>

</div>

<a href="index.php?controller=partida&method=terminarPartida" class="boton-flotante">
    Terminar partida
</a>

<script src="public/js/temporizador.js"></script>

<?php include("views/partials/footer.php"); ?>