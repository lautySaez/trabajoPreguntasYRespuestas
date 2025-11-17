<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/adminDashboard.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) {
    header("Location: index.php?controller=LoginController&method=inicioSesion");
    exit;
}
?>

<div class="admin-app">
    <aside class="admin-sidebar">
        <div class="brand">
            <h2>Deberes · Admin</h2>
        </div>

        <nav class="admin-nav">
            <a href="index.php?controller=admin&method=homeAdmin" class="active"><i class="fa fa-chart-line"></i> Dashboard</a>
            <a href="index.php?controller=admin&method=informes"><i class="fa fa-flag"></i> Informes de Editores</a>
            <a href="index.php?controller=admin&method=reportes"><i class="fa fa-exclamation-circle"></i> Reportes de Jugadores</a>
            <a href="index.php?controller=editor&method=gestionarPreguntas"><i class="fa fa-edit"></i> Gestión de Preguntas (Editor)</a>
        </nav>

        <div class="sidebar-footer">
            <small>Conectado como</small>
            <strong><?= htmlspecialchars($usuario['nombre_usuario']) ?></strong>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <h1>Panel Administrativo</h1>
            </div>
            <div class="topbar-right">
                <?php if (!empty($usuario['foto_perfil'])): ?>
                    <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" alt="avatar" class="top-avatar">
                <?php else: ?>
                    <div class="avatar-placeholder"><?= htmlspecialchars(substr($usuario['nombre_usuario'],0,1)) ?></div>
                <?php endif; ?>
                <div class="top-user">
                    <div><?= htmlspecialchars($usuario['nombre']) ?></div>
                    <small><?= htmlspecialchars($usuario['email']) ?></small>
                </div>
            </div>
        </header>

        <section class="kpis">
            <div class="kpi-card">
                <div class="kpi-title">Total preguntas</div>
                <div class="kpi-value" id="kpi-total-preguntas">--</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Categorías</div>
                <div class="kpi-value" id="kpi-categorias">--</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Jugadores registrados</div>
                <div class="kpi-value" id="kpi-jugadores"><?= htmlspecialchars($kpis['total_usuarios'] ?? '—') ?></div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title">Partidas totales</div>
                <div class="kpi-value" id="kpi-partidas">--</div>
            </div>
        </section>

        <section class="charts-grid">
            <div class="card">
                <h3>Distribución por edades</h3>
                <canvas id="chart-edades"></canvas>
            </div>

            <div class="card">
                <h3>Distribución por género</h3>
                <canvas id="chart-genero"></canvas>
            </div>

            <div class="card">
                <h3>Preguntas por categoría</h3>
                <canvas id="chart-categorias"></canvas>
            </div>

            <div class="card">
                <h3>Mapa: dónde se juega</h3>
                <div id="mapa-usuarios" style="height:220px;"></div>
            </div>
        </section>

        <section class="tables-grid">
            <div class="card full">
                <h3>Top 10 Jugadores</h3>
                <table class="table">
                    <thead><tr><th>Avatar</th><th>Usuario</th><th>Puntos</th><th>Partidas</th></tr></thead>
                    <tbody id="top-jugadores-body">
                    <tr><td colspan="4">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h3>Top 10 preguntas más fáciles</h3>
                <ul id="top-faciles-list" class="compact-list"></ul>
            </div>

            <div class="card">
                <h3>Últimos informes</h3>
                <ul id="ultimos-informes-list" class="compact-list"></ul>
            </div>
        </section>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="public/js/adminDashboard.js"></script>

<?php include("views/partials/footer.php"); ?>