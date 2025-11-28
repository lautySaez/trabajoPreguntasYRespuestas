<?php include("views/partials/header.php"); ?>

<div class="resultado-partida" style="text-align:center; padding:50px;">
    <h1>¡Partida finalizada!</h1>
    <p>Tu puntaje final: <strong><?= htmlspecialchars($puntaje) ?> puntos</strong></p>
    <a href="/home" class="boton-partida">Volver al inicio</a>
</div>

<?php include("views/partials/footer.php"); ?>
