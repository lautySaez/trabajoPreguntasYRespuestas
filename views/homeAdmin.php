<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="public/css/adminDashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    header("Location: index.php?controller=LoginController&method=inicioSesion");
    exit;
}
?>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="brand">Deberes · Admin</div>

            <nav>
                <a href="index.php?controller=admin&method=homeAdmin" class="active">
                    <i class="fa fa-chart-line"></i> Dashboard
                </a>
                <a href="index.php?controller=admin&method=informes">
                    <i class="fa fa-flag"></i> Informes de Editores
                </a>
                <a href="index.php?controller=admin&method=reportes">
                    <i class="fa fa-exclamation-circle"></i> Reportes de Jugadores
                </a>
            </nav>

            <div class="sidebar-user">
                <small>Conectado como</small>
                <strong><?= htmlspecialchars($usuario['nombre_usuario']) ?></strong>
            </div>
        </aside>

        <main class="content">

            <header class="topbar">
                <h1>Panel Administrativo</h1>

                <div class="user-info">
                    <?php if (!empty($usuario['foto_perfil'])): ?>
                        <img src="<?= htmlspecialchars($usuario['foto_perfil']) ?>" class="avatar">
                    <?php else: ?>
                        <div class="avatar placeholder"><?= strtoupper(substr($usuario['nombre_usuario'],0,1)) ?></div>
                    <?php endif; ?>

                    <div>
                        <div><?= htmlspecialchars($usuario['nombre']) ?></div>
                        <small><?= htmlspecialchars($usuario['email']) ?></small>
                    </div>
                </div>
            </header>

            <section class="kpi-grid">
                <div class="kpi-card">
                    <span>Total preguntas</span>
                    <strong id="kpi-total-preguntas">--</strong>
                </div>
                <div class="kpi-card">
                    <span>Categorías</span>
                    <strong id="kpi-categorias">--</strong>
                </div>
                <div class="kpi-card">
                    <span>Partidas</span>
                    <strong id="kpi-partidas">--</strong>
                </div>
            </section>

            <section class="chart-grid">
                <div class="chart-card">
                    <h3>Distribución por edades</h3>
                    <canvas id="chart-edades"></canvas>
                </div>

                <div class="chart-card">
                    <h3>Distribución por género</h3>
                    <canvas id="chart-genero"></canvas>
                </div>

                <div class="chart-card">
                    <h3>Preguntas por categoría</h3>
                    <canvas id="chart-categorias"></canvas>
                </div>

                <div class="chart-card">
                    <h3>Ciudades con más partidas</h3>
                    <canvas id="chart-lugares"></canvas>
                </div>
            </section>

            <section class="table-grid">

                <div class="table-card">
                    <h3>Top 10 jugadores</h3>
                    <table>
                        <thead>
                        <tr><th>Avatar</th><th>Usuario</th><th>Puntos</th><th>Partidas</th></tr>
                        </thead>
                        <tbody id="top-jugadores-body">
                        <tr><td colspan="4">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-card small">
                    <h3>Preguntas más fáciles</h3>
                    <ul id="top-faciles-list" class="compact-list"></ul>
                </div>

                <div class="table-card small">
                    <h3>Últimos informes</h3>
                    <ul id="ultimos-informes-list" class="compact-list"></ul>
                </div>

            </section>

        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="public/js/adminDashboard.js"></script>

<?php include("views/partials/footer.php"); ?>