<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/home.css">
<link rel="stylesheet" href="public/css/rankings.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<div class="ranking-container">

    <h2 class="ranking-title">🏆 Rankings</h2>

    <!-- Filtro -->
    <form method="GET" class="ranking-filter">
        <input type="hidden" name="controller" value="RankingController">
        <input type="hidden" name="method" value="verRankings">

        <select name="tipo" class="filter-select" onchange="this.form.submit()">
            <option value="diario" <?= ($_GET["tipo"] ?? "diario") === "diario" ? "selected" : "" ?>>Top Diario</option>
            <option value="semanal" <?= ($_GET["tipo"] ?? "") === "semanal" ? "selected" : "" ?>>Top Semanal</option>
            <option value="mensual" <?= ($_GET["tipo"] ?? "") === "mensual" ? "selected" : "" ?>>Top Mensual</option>
        </select>
    </form>

    <!-- Tabla ranking -->
    <table class="ranking-table">
        <thead>
            <tr>
                <th>Avatar</th>
                <th>Usuario</th>
                <th>Puntos</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rankings as $r): ?>
                    <tr>
                        <td>
                            <?php if (!empty($r["foto_perfil"])): ?>
                                <img src="<?= htmlspecialchars($r["foto_perfil"]) ?>"
                                    width="50" height="50" style="border-radius:50%;">
                            <?php else: ?>
                                <div style="
                                    width:50px;height:50px;border-radius:50%;
                                    background:#fff;display:inline-block;">
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r["nombre_usuario"]) ?></td>
                        <td><?= htmlspecialchars($r["puntaje_total"]) ?></td>
                    </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php include("views/partials/footer.php"); ?>