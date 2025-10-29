<?php include("views/partials/header.php"); ?>

<div class="modo-container">
    <h2>Seleccioná el modo de juego</h2>
    <p>Elegí si querés enfrentarte a otro jugador (PvP) o al BOT (PvB) </p>

    <form method="POST" action="index.php?controller=partida&method=guardarModo">
        <button type="submit" name="modo" value="bot" class="modo-boton">Jugar contra BOT </button>
        <button type="submit" name="modo" value="jugador" class="modo-boton">Jugar contra otro jugador </button>
    </form>

    <a href="index.php?controller=LoginController&method=home" class="volver">Volver al inicio</a>
</div>

<link rel="stylesheet" href="public/css/modoDeJuego.css">

<?php include("views/partials/footer.php"); ?>

