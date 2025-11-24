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
        <?php
        $hayError = !empty($_SESSION['partida_finalizada']);
        $rondaCompletada = !empty($_SESSION['ronda_completada']);
        $preguntasSesion = $_SESSION['preguntas'] ?? [];
        $indiceSiguiente = $_SESSION['pregunta_actual'] ?? null;
        $haySiguiente = (!$hayError && !$rondaCompletada && $preguntasSesion && $indiceSiguiente !== null);
        ?>
        <?php if ($hayError): ?>
            <div class="mensaje-fin">
                Respuesta incorrecta. La ronda termina aquí.
            </div>
            <a href="index.php?controller=partida&method=mostrarRuleta" class="btn btn-ruleta">Girar ruleta</a>
        <?php elseif ($rondaCompletada): ?>
            <div class="mensaje-exito">
                ¡Completaste todas las preguntas de la categoría sin errores!
            </div>
            <a href="index.php?controller=partida&method=mostrarRuleta" class="btn btn-ruleta">Girar ruleta</a>
            <?php unset($_SESSION['ronda_completada']); ?>
        <?php elseif ($haySiguiente): ?>
        <!-- Sgte Pregunta-->
            <a href="index.php?controller=partida&method=continuarRonda" class="btn btn-siguiente">Siguiente pregunta</a>
        <!-- Ruleta -->
            <a href="index.php?controller=partida&method=mostrarRuleta" class="btn btn-ruleta">Cambiar categoría</a>
        <?php else: ?>
            <a href="index.php?controller=partida&method=mostrarRuleta" class="btn btn-ruleta">Girar ruleta</a>
        <?php endif; ?>
        <!-- Reportar -->
        <form method="POST" action="index.php?controller=partida&method=reportarPregunta" class="form-reportar">
            <input type="hidden" name="id_pregunta" value="<?= $preguntaActual["id"] ?>">
            <button type="button" class="boton-reportar" onclick="abrirModalReporte()">
            Reportar</button>
        </form>
        <!-- Popup Reporte -->
        <div id="modalReporte" class="modal-reporte">
            <div class="modal-contenido">
                <h3>Reportar pregunta</h3>

                <form id="formReporte" method="POST" action="index.php?controller=partida&method=reportarPregunta">
                    <input type="hidden" name="id_pregunta" value="<?= $preguntaActual["id"] ?>">

                    <label for="motivo">Motivo del reporte:</label>
                    <textarea name="motivo" id="motivo"></textarea>

                    <div class="modal-botones">
                        <button type="submit" class="btn-enviar">Enviar reporte</button>
                        <button type="button" class="btn-cancelar" onclick="cerrarModalReporte()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Terminar -->
        <a href="index.php?controller=partida&method=terminarPartida" class="btn btn-terminar">Terminar partida</a>
    </div>

    <div class="puntaje-actual">
        Puntaje acumulado: <strong><?= (int)($_SESSION['puntaje'] ?? 0) ?></strong>
        <?php if (!empty($_SESSION['partida_finalizada'])): ?>
            <div class="nota-eliminado">La ronda terminó por respuesta incorrecta.</div>
        <?php endif; ?>
    </div>
</div>

<script src="public/js/feedback.js" defer></script>
<?php include("views/partials/footer.php"); ?>