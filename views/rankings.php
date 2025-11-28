<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="/public/css/rankings.css">
<link rel="stylesheet" href="/public/css/estilos.css">

<?php
$rankingJugadores = $rankingJugadores ?? [];
?>

<div class="gestionar-categorias-page">
    <h1>Tabla de Posiciones</h1>

    <div class="listado-categorias">
        <div class="header-row">
        <h2>
            <?php if (($tipo ?? '') === 'mejores'): ?>
                🏆 Mejores Puntajes en una Partida
            <?php else: ?>
                🐐 Los GOAT (Puntaje Acumulado)
            <?php endif; ?>
        </h2>

        <!-- Filtro -->
            <div class="filtros-container">
                <form method="GET" class="filtro-form">
                    <input type="hidden" name="controller" value="RankingController">
                    <input type="hidden" name="method" value="verRankings">

                    <select name="tipo" class="filter-select" onchange="this.form.submit()">
                        <option value="goat" <?= ($_GET["tipo"] ?? "goat") === "goat" ? "selected" : "" ?>>
                            Los GOAT
                        </option>
                        <option value="mejores" <?= ($_GET["tipo"] ?? "") === "mejores" ? "selected" : "" ?>>
                            Mejores Partidas
                        </option>
                    </select>
                </form>
            </div>
        </div>
            <!-- Rankings -->
            <table class="tabla-categorias">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Avatar</th>
                        <th>Usuario</th>
                        <th>Puntos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top3)): ?>
                        <?php foreach ($top3 as $i => $jugador): ?>
                            <tr class="rank-fila" onclick="window.location.href='/usuario/publico/<?= urlencode($jugador['nombre_usuario']) ?>'">
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <?php if (!empty($jugador["foto_perfil"])): ?>
                                        <img src="<?= htmlspecialchars($jugador["foto_perfil"]) ?>"
                                            class="rank-avatar">
                                    <?php else: ?>
                                        <div class="rank-avatar-placeholder"></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($jugador["nombre_usuario"]) ?></td>
                                <td>
                                    <?php if (($tipo ?? '') === 'mejores'): ?>
                                        <?= htmlspecialchars($jugador["mejor_partida"]) ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($jugador["puntaje_total"]) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-resultados">No hay jugadores registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
    </div>
</div>

<?php include("views/partials/footer.php"); ?>