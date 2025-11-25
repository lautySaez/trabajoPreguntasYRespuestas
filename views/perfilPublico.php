<?php
include(__DIR__ . "/partials/header.php");
?>
    <link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/perfilPublico.css">

    <div class="perfil-publico-container">

        <div class="perfil-header">
            <?php
            $nombre_usuario = htmlspecialchars($datos['nombre_usuario'] ?? 'Usuario Desconocido');
            $puntos_totales = $datos['puntos_totales'] ?? 0;
            $partidas_jugadas = $datos['partidas_jugadas'] ?? 0;

            if (!empty($datos['foto_perfil'])): ?>
                <img src="<?= htmlspecialchars($datos['foto_perfil']) ?>" alt="Avatar" class="avatar-grande">
            <?php else:
                $initial = strtoupper(substr($nombre_usuario, 0, 1));
                ?>
                <div class="avatar-grande placeholder">
                    <?= $initial ?>
                </div>
            <?php endif; ?>

            <h1><?= $nombre_usuario ?></h1>
            <p>Jugador</p>
        </div>

        <div class="estadisticas-card">
            <h3>📊 Estadísticas de Juego</h3>
            <div class="stat-item">
                <p>Puntos Totales:</p>
                <strong><?= number_format($puntos_totales, 0, ',', '.') ?></strong>
            </div>
            <div class="stat-item">
                <p>Partidas Jugadas:</p>
                <strong><?= number_format($partidas_jugadas, 0, ',', '.') ?></strong>
            </div>
        </div>

        <div class="volver">
            <a href="/trabajoPreguntasYRespuestas/home">Volver al Inicio</a>
        </div>
    </div>

<?php
include(__DIR__ . "/partials/footer.php");
?>