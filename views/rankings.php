<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/rankings.css">

<?php
$rankingJugadores = $rankingJugadores ?? [];
?>

<div class="gestionar-categorias-page">
    <h1>Tabla de Posiciones</h1>

    <!-- Filtro -->
    <div class="filtros-container">
        <form method="GET" class="ranking-filter">
            <input type="hidden" name="controller" value="RankingController">
            <input type="hidden" name="method" value="verRankings">

            <select name="tipo" class="filter-select" onchange="this.form.submit()">
                <option value="diario" <?= ($_GET["tipo"] ?? "diario") === "diario" ? "selected" : "" ?>>Diario</option>
                <option value="semanal" <?= ($_GET["tipo"] ?? "") === "semanal" ? "selected" : "" ?>>Semanal</option>
                <option value="mensual" <?= ($_GET["tipo"] ?? "") === "mensual" ? "selected" : "" ?>>Mensual</option>
            </select>
        </form>
    </div>

    <div class="listado-categorias">
        <h2>📊 Los Mas Rankeados</h2>
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
                                        class="rank-avatar" width="50" height="50" style="border-radius:50%;">
                                <?php else: ?>
                                    <div style="width:50px;height:50px;border-radius:50%;background:#ccc;"></div>
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