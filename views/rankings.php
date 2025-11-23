<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/rankings.css">
<link rel="stylesheet" href="public/css/estilos.css">

<?php
$rankingJugadores = $rankingJugadores ?? [];
?>

<div class="gestionar-categorias-page">
    <h1>Tabla de Posiciones</h1>

    <div class="listado-categorias">
        <div class="header-row">
        <h2>📊 Los Mas Rankeados</h2>
        <!-- Filtro -->
            <div class="filtros-container">
                <form method="GET" class="ranking-filter">
                    <input type="hidden" name="controller" value="RankingController">
                    <input type="hidden" name="method" value="verRankings">

                    <select name="tipo" class="filter-select" onchange="this.form.submit()">
                        <option value="invierno" <?= ($_GET["tipo"] ?? "invierno") === "invierno" ? "selected" : "" ?>>Temporada Invierno</option>
                        <option value="verano" <?= ($_GET["tipo"] ?? "") === "verano" ? "selected" : "" ?>>Temporada Verano</option>
                        <option value="goat" <?= ($_GET["tipo"] ?? "") === "goat" ? "selected" : "" ?>>GOAT</option>
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
                            <tr>
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
                                <td><?= htmlspecialchars($jugador["puntaje_total"]) ?></td>
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