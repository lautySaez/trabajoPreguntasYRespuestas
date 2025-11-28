<?php include("views/partials/header.php"); ?>

<div class="partida-contenedor">

<?php if (!empty($preguntaActual) && !empty($preguntaActual["respuestas"])): ?>

    <h2><?= htmlspecialchars($preguntaActual["pregunta"]) ?></h2>

    <div class="respuestas-grid">
        <?php foreach ($preguntaActual["respuestas"] as $index => $respuesta): ?>
            <form method="POST" action="/partida/responderPregunta" style="display:inline;">
                <!-- Usar directamente la clave/ID de la respuesta (no sumar +1) -->
                <input type="hidden" name="respuesta" value="<?= (int)$index ?>">
                <button type="submit">
                    <?= htmlspecialchars($respuesta["texto"]) ?>
                </button>
            </form>
        <?php endforeach; ?>
    </div>

    <!-- Temporizador -->
    <div id="temporizador" class="badge bg-danger fs-5 mt-2"></div>

    <!-- Form automático cuando el tiempo se acaba -->
    <form id="form-timeout" method="POST" action="/partida/responderPregunta">
        <input type="hidden" name="respuesta" value="timeout">
    </form>

<?php else: ?>

    <p>No hay preguntas disponibles para esta categoría.</p>

<?php endif; ?>

</div>

<a href="/partida/terminarPartida" class="boton-flotante">Terminar partida</a>

<?php $ts = time(); ?>
<script>window.tiempoRestante = <?= isset($tiempoRestante) ? (int)$tiempoRestante : 10 ?>;</script>
<script src="/public/js/temporizador.js?v=<?= $ts ?>"></script>

<?php include("views/partials/footer.php"); ?>
