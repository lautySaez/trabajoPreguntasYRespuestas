<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$flash = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sugerir Pregunta</title>
    <link rel="stylesheet" href="/public/estilos.css" />
    <link rel="stylesheet" href="/public/sugerencias.css" />
</head>

<body>
    <div class="form-sugerencia">
        <h2>Sugerir Pregunta</h2>
        <?php if ($flash): ?><div class="flash-error"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
        <form method="post" action="/sugerencia/guardar">
            <label for="categoria_id">Categoría</label>
            <select name="categoria_id" id="categoria_id" required>
                <option value="">-- Seleccione --</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="pregunta">Texto de la pregunta</label>
            <input type="text" name="pregunta" id="pregunta" maxlength="255" required placeholder="Ej: ¿Cuál es la capital de Francia?" />

            <label>Respuestas (indique la correcta marcando el círculo)</label>
            <div class="respuestas-grid">
                <input type="text" name="respuesta_1" maxlength="120" required placeholder="Respuesta 1" />
                <input type="radio" name="respuesta_correcta" value="1" required />
                <input type="text" name="respuesta_2" maxlength="120" required placeholder="Respuesta 2" />
                <input type="radio" name="respuesta_correcta" value="2" />
                <input type="text" name="respuesta_3" maxlength="120" required placeholder="Respuesta 3" />
                <input type="radio" name="respuesta_correcta" value="3" />
                <input type="text" name="respuesta_4" maxlength="120" required placeholder="Respuesta 4" />
                <input type="radio" name="respuesta_correcta" value="4" />
            </div>
            <div class="botones">
                <button type="submit">Enviar sugerencia</button>
                <a href="/partida/mostrarRuleta">Cancelar</a>
            </div>
        </form>
    </div>
</body>

</html>