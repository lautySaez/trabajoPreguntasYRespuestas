<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/reglas.css">

    <div class="reglas-container">
    <h2>Reglas del Juego</h2>

    <ul>
        <li>Cada partida consta de 10 preguntas.</li>
        <li>Tenés un tiempo límite de 20 segundos por pregunta.</li>
        <li>Si se acaba tu tiempo de respuesta la pregunta se considera incorrecta.</li>
        <li>Cada pregunta correcta suma +2 puntos.</li>
        <li>Cada pregunta incorrecta resta -1 punto.</li>
        <li>Si finalizás como ganador se suman +5 puntos.</li>
        <li>Si finalizás como perdedor se restan -3 puntos.</li>
    </ul>

    <a href="ruleta" class="boton-partida">Comenzar</a>
</div>

<?php include("views/partials/footer.php"); ?>