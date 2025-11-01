<?php include("views/partials/header.php"); ?>

<div class="partida-contenedor">
    <!-- Pregunta -->
    <h2><?= htmlspecialchars($preguntaActual["texto"]) ?></h2>

    <!-- Respuestas en grid 2x2 -->
    <div class="respuestas-grid">
        <?php foreach ($preguntaActual["respuestas"] as $respuesta): ?>
            <button type="button"><?= htmlspecialchars($respuesta["texto"]) ?></button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Btn terminar partida flotante -->
<a href="index.php?controller=partida&method=terminarPartida" class="boton-flotante">
    Terminar partida
</a>

<?php include("views/partials/footer.php"); ?>
