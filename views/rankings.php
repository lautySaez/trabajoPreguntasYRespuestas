<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/home.css">
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
                <th>#</th>
                <th>Avatar</th>
                <th>Usuario</th>
                <th>Puntos</th>
            </tr>
        </thead>
        <tbody>
            <!-- DATOS HARDCODEADOS POR AHORA -->
            <tr>
                <td>1</td>
                <td><img src="public/img/avatar1.png" class="rank-avatar"></td>
                <td>Martin</td>
                <td>2450</td>
            </tr>
            <tr>
                <td>2</td>
                <td><img src="public/img/avatar2.png" class="rank-avatar"></td>
                <td>Gaston</td>
                <td>2190</td>
            </tr>
            <tr>
                <td>3</td>
                <td><img src="public/img/avatar3.png" class="rank-avatar"></td>
                <td>Sebas</td>
                <td>1800</td>
            </tr>
        </tbody>
    </table>

</div>

<?php include("views/partials/footer.php"); ?>