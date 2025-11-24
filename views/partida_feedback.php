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
        $partidaFinalizada = !empty($_SESSION['partida_finalizada']);
        $tiempoAgotado = !empty($_SESSION['tiempo_agotado']);
        ?>
        <?php if ($partidaFinalizada): ?>
            <div class="mensaje-fin">
                <?php if ($tiempoAgotado): ?>
                    Tiempo agotado. La ronda termina aquí.
                <?php else: ?>
                    Respuesta incorrecta. La ronda termina aquí.
                <?php endif; ?>
            </div>
            <!-- Sin botón de ruleta al fallar o expirar tiempo -->
        <?php else: ?>
            <div class="mensaje-exito">Respuesta correcta.</div>
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
        <?php if ($partidaFinalizada): ?>
            <div class="nota-eliminado">
                <?php if ($tiempoAgotado): ?>
                    Fin de ronda por tiempo agotado.
                <?php else: ?>
                    La ronda terminó por respuesta incorrecta.
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="public/js/feedback.js" defer></script>
<?php include("views/partials/footer.php"); ?>