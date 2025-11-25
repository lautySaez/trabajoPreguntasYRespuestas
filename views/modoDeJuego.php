<?php include("views/partials/header.php"); ?>

<div class="modo-container">
    <h2>Seleccioná el modo de juego</h2>
    <p>Elegí que tipo de partida deseas jugar</p>

    <form method="POST" action="/trabajoPreguntasYRespuestas/partida/guardarModo">
        <button type="submit" name="modo" value="bot" class="modo-boton">Partida Normal</button>
        <!--<button type="submit" name="modo" value="jugador" class="modo-boton">Jugar contra otro jugador </button>-->
    </form>

    <a href="/trabajoPreguntasYRespuestas/home" class="volver">Volver al inicio</a>
</div>

<link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/modoDeJuego.css">

<?php include("views/partials/footer.php"); ?>

