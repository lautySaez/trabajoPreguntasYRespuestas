<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/ruleta.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<div class="ruleta-page">
    <div class="ruleta-container">

        <h2>¡Girá la ruleta para elegir categoría inicial!</h2>

        <div class="ruleta-wrapper">
            <div class="flecha"></div>
            <canvas id="ruleta" width="400" height="400"></canvas>
        </div>

        <button id="boton-girar">Girar</button>
    </div>
</div>

<div id="resultado">
    <div class="resultado-card">
        <p id="categoria-elegida"></p>
        <a id="btn-iniciar" class="boton-iniciar" href="#">Iniciar partida</a>
    </div>
</div>

<script src="public/js/ruleta.js" type="text/javascript"></script>

<?php include("views/partials/footer.php"); ?>

