<?php include("views/partials/header.php"); ?>
<link rel="stylesheet" href="public/css/feedback.css">
<div class="partida-contenedor feedback">
    <h2><?= htmlspecialchars($preguntaActual["pregunta"]) ?></h2>

    <div class="respuestas-grid">
        <?php foreach ($preguntaActual["respuestas"] as $respuesta):
            $id = (int)$respuesta['id'];
            $texto = $respuesta['texto'];
            $clases = ['respuesta-item'];
            if ($id === $respuestaCorrectaId) {
                $clases[] = 'correcta';
            } elseif ($id === $respuestaSeleccionadaId) {
                $clases[] = 'incorrecta';
            } else {
                $clases[] = 'neutral';
            }
        ?>
            <div class="<?= implode(' ', $clases) ?>">
                <span class="letra-opcion"><?= chr(64 + $id) ?>)</span>
                <span class="texto-opcion"><?= htmlspecialchars($texto) ?></span>
                <?php if ($id === $respuestaCorrectaId): ?>
                    <span class="badge-estado correcto">✔ Correcta</span>
                <?php elseif ($id === $respuestaSeleccionadaId): ?>
                    <span class="badge-estado incorrecto">✘ Tu elección</span>
                <?php else: ?>
                    <span class="badge-estado">&nbsp;</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="acciones-post-respuesta">
        <?php if (empty($_SESSION['partida_finalizada'])): ?>
            <a href="index.php?controller=partida&method=mostrarRuleta" class="btn btn-ruleta">Girar ruleta</a>
        <?php else: ?>
            <div class="mensaje-fin">
                Respuesta incorrecta. La partida ha finalizado.
            </div>
        <?php endif; ?>
        <a href="index.php?controller=partida&method=terminarPartida" class="btn btn-terminar">Terminar partida</a>
    </div>

    <div class="puntaje-actual">
        Puntaje acumulado: <strong><?= (int)($_SESSION['puntaje'] ?? 0) ?></strong>
        <?php if (!empty($_SESSION['partida_finalizada'])): ?>
            <div class="nota-eliminado">No podés seguir girando: fallaste esta pregunta.</div>
        <?php endif; ?>
    </div>
</div>

<?php include("views/partials/footer.php"); ?>