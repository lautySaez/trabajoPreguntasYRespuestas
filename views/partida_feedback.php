<?php include("views/partials/header.php"); ?>
<link rel="stylesheet" href="/public/css/feedback.css">
<?php
// Cargar categorías activas para el popup de sugerencia
require_once("models/SugerenciaModel.php");
$__sugModel = new SugerenciaModel();
$__categoriasActivas = $__sugModel->obtenerCategoriasActivas();
?>
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
            <a href="/partida/mostrarRuleta" class="btn btn-ruleta">Girar ruleta</a>
        <?php endif; ?>
        <!-- Sugerir nueva pregunta -->
        <button type="button" id="btnAbrirSugerencia" class="btn btn-sugerir">Sugerir pregunta</button>
        <!-- Reportar -->
        <form method="POST" action="/partida/reportarPregunta" class="form-reportar">
            <input type="hidden" name="id_pregunta" value="<?= $preguntaActual["id"] ?>">
            <button type="button" class="boton-reportar" onclick="abrirModalReporte()">
                Reportar</button>
        </form>
        <!-- Popup Reporte -->
        <div id="modalReporte" class="modal-reporte">
            <div class="modal-contenido">
                <h3>Reportar pregunta</h3>

                <form id="formReporte" method="POST" action="/partida/reportarPregunta">
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
        <a href="/partida/terminarPartida" class="btn btn-terminar">Terminar partida</a>
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

<!-- Modal Sugerencia -->
<div id="modalSugerencia" class="modal-sugerencia" style="display:none;">
    <div class="modal-contenido">
        <button type="button" class="modal-cerrar" id="cerrarModalSug">×</button>
        <h3>Sugerir Pregunta</h3>
        <?php
        $flashSugError = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        $sugData = $_SESSION['sug_data'] ?? null;
        unset($_SESSION['sug_data']);
        $shouldOpen = $flashSugError ? 'true' : 'false';
        ?>
        <?php if ($flashSugError): ?>
            <div class="sug-error"><?= htmlspecialchars($flashSugError) ?></div>
        <?php endif; ?>
        <form id="formSugerencia" method="post" action="/sugerencia/guardar">
            <label for="categoria_id">Categoría</label>
            <select name="categoria_id" id="categoria_id" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($__categoriasActivas as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= ($sugData && (int)$sugData['categoria_id'] === (int)$c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="preguntaTexto">Pregunta</label>
            <input type="text" name="pregunta" id="preguntaTexto" maxlength="255" required value="<?= $sugData ? htmlspecialchars($sugData['pregunta']) : '' ?>">
            <fieldset class="sug-respuestas">
                <legend>Respuestas (marque la correcta)</legend>
                <div class="fila-resp">
                    <input type="text" name="respuesta_1" maxlength="120" required placeholder="Respuesta 1" value="<?= $sugData ? htmlspecialchars($sugData['r1']) : '' ?>">
                    <input type="radio" name="respuesta_correcta" value="1" required <?= ($sugData && (int)$sugData['correcta'] === 1) ? 'checked' : '' ?>>
                </div>
                <div class="fila-resp">
                    <input type="text" name="respuesta_2" maxlength="120" required placeholder="Respuesta 2" value="<?= $sugData ? htmlspecialchars($sugData['r2']) : '' ?>">
                    <input type="radio" name="respuesta_correcta" value="2" <?= ($sugData && (int)$sugData['correcta'] === 2) ? 'checked' : '' ?>>
                </div>
                <div class="fila-resp">
                    <input type="text" name="respuesta_3" maxlength="120" required placeholder="Respuesta 3" value="<?= $sugData ? htmlspecialchars($sugData['r3']) : '' ?>">
                    <input type="radio" name="respuesta_correcta" value="3" <?= ($sugData && (int)$sugData['correcta'] === 3) ? 'checked' : '' ?>>
                </div>
                <div class="fila-resp">
                    <input type="text" name="respuesta_4" maxlength="120" required placeholder="Respuesta 4" value="<?= $sugData ? htmlspecialchars($sugData['r4']) : '' ?>">
                    <input type="radio" name="respuesta_correcta" value="4" <?= ($sugData && (int)$sugData['correcta'] === 4) ? 'checked' : '' ?>>
                </div>
            </fieldset>
            <input type="hidden" name="from_modal" value="1">
            <input type="hidden" name="origin_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
            <div class="acciones-modal">
                <button type="submit" class="btn">Enviar</button>
                <button type="button" id="cancelarSugerencia" class="btn secundario">Cancelar</button>
            </div>
            <p class="nota-limite">Máximo 2 sugerencias por día. Evite duplicados.</p>
        </form>
    </div>
</div>

<style>
    .btn.btn-sugerir {
        background: #d39a41;
        color: #fff;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        border: unset;
    }

    .btn.btn-sugerir:hover {
        transform: translateY(-2px);
    }

    .boton-reportar {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
        font-size: 14px;
    }
    .boton-reportar:hover {
        transform: translateY(-2px);
    }

    .modal-sugerencia {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, .55);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-contenido {
        background: #fff;
        width: 520px;
        max-width: 95%;
        padding: 20px 24px;
        border-radius: 8px;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .25);
        position: relative;
    }

    .modal-contenido h3 {
        margin-top: 0;
        font-size: 1.3rem;
    }

    .modal-contenido label {
        margin-top: 12px;
        display: block;
        font-weight: 600;
    }

    .modal-contenido input[type=text],
    .modal-contenido select {
        width: 100%;
        padding: 8px;
        margin-top: 4px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    fieldset.sug-respuestas {
        border: 1px solid #ddd;
        padding: 10px 12px;
        margin-top: 14px;
        border-radius: 6px;
    }

    .fila-resp {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 8px;
    }

    .fila-resp input[type=text] {
        flex: 1;
    }

    .acciones-modal {
        display: flex;
        gap: 12px;
        margin-top: 18px;
    }

    .acciones-modal .btn {
        background: #2c7a2c;
        color: #fff;
        padding: 8px 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .acciones-modal .btn.secundario {
        background: #777;
    }

    .modal-cerrar {
        position: absolute;
        top: 8px;
        right: 10px;
        background: transparent;
        border: none;
        font-size: 1.4rem;
        cursor: pointer;
    }

    .nota-limite {
        font-size: .8rem;
        color: #555;
        margin-top: 10px;
    }

    .sug-error {
        background: #ffe0e0;
        color: #b00000;
        padding: 10px 12px;
        border: 1px solid #e5b4b4;
        border-radius: 6px;
        font-size: .8rem;
        margin-bottom: 8px;
    }

    @media (max-width:600px) {
        .modal-contenido {
            width: 95%;
        }
    }
</style>

<script>
    (function() {
        const btn = document.getElementById('btnAbrirSugerencia');
        const modal = document.getElementById('modalSugerencia');
        const cerrar = document.getElementById('cerrarModalSug');
        const cancelar = document.getElementById('cancelarSugerencia');

        function abrir() {
            modal.style.display = 'flex';
        }

        function cerrarModal() {
            modal.style.display = 'none';
        }
        btn && btn.addEventListener('click', abrir);
        cerrar && cerrar.addEventListener('click', cerrarModal);
        cancelar && cancelar.addEventListener('click', cerrarModal);
        window.addEventListener('keydown', e => {
            if (e.key === 'Escape') cerrarModal();
        });
        modal && modal.addEventListener('click', e => {
            if (e.target === modal) cerrarModal();
        });
        const shouldOpen = <?= $shouldOpen ?>;
        if (shouldOpen) {
            abrir();
        }
    })();
</script>
<script src="/public/js/feedback.js" defer></script>
<?php include("views/partials/footer.php"); ?>