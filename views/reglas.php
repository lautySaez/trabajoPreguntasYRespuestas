<?php include("views/partials/header.php"); ?>

<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 20vh; text-align: center; gap: 20px;">

    <h2>Reglas del Juego</h2>

    <ul style="text-align: left; max-width: 400px;">
        <li>Cada partida consta de 10 preguntas.</li>
        <li>Tenés un tiempo límite de 20 segundos por pregunta.</li>
        <li>Si respondés incorrectamente, la partida termina.</li>
        <li>Responder correctamente todas las preguntas te da la máxima puntuación y participarás en los rankings.</li>
    </ul>

    <a href="index.php?controller=partida&method=iniciarPartida" class="boton-partida">Comenzar</a>

</div>

<?php include("views/partials/footer.php"); ?>
