<?php include("views/partials/header.php"); ?>

<?php
/** @var array $reportes */
?>

<link rel="stylesheet" href="/public/css/adminReportes.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) {
    header("Location: /login");
    exit;
}
?>

<div class="admin-app">

    <main class="admin-main">

        <header class="admin-topbar">
            <h1>Reportes de Jugadores</h1>
        </header>

        <div class="card full">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Pregunta</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($reportes as $rep): ?>
                    <tr>
                        <td><?= $rep['id'] ?></td>
                        <td><?= htmlspecialchars($rep['usuario_nombre']) ?></td>
                        <td><?= htmlspecialchars(substr($rep['pregunta_texto'], 0, 80)) ?>...</td>
                        <td><?= htmlspecialchars(substr($rep['motivo'], 0, 60)) ?>...</td>
                        <td><?= $rep['fecha_reporte'] ?></td>

                        <td>
                            <button class="btn btn-green btn-ver-mas" data-id="<?= $rep['id'] ?>">Ver más</button>

                                <form method="POST"
                                    action="/admin/accionReporte"
                                  style="display:inline;"
                                  onsubmit="return confirm('¿Está seguro de eliminar este reporte de forma directa?');">

                                <input type="hidden" name="id" value="<?= $rep['id'] ?>">
                                <input type="hidden" name="accion" value="eliminar">

                                <button class="btn btn-red">Borrar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="back-link-container" style="text-align: right; margin-top: 20px;">
            <a href="/admin/homeAdmin" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>
    </main>
</div>

<div id="detalleModal" class="reporte-modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3>Detalle del Reporte #<span id="reporte-id-display"></span></h3>
        <hr>

        <h4>Información del Reporte:</h4>
        <p><strong>Motivo:</strong> <span id="detalle-motivo"></span></p>
        <p><strong>Fecha:</strong> <span id="detalle-fecha"></span></p>

        <h4>Reportado por:</h4>
        <p><strong>Usuario:</strong> <span id="detalle-usuario"></span> (<span id="detalle-email"></span>)</p>

        <h4 style="margin-top: 15px;">Pregunta Reportada:</h4>
        <p><strong>Texto:</strong> <span id="detalle-pregunta"></span></p>

        <ul id="detalle-respuestas">
            <li>R1: <span id="res1"></span></li>
            <li>R2: <span id="res2"></span></li>
            <li>R3: <span id="res3"></span></li>
            <li>R4: <span id="res4"></span></li>
            <li style="color: green; font-weight: bold; margin-top: 10px;">Correcta: <span id="res-correcta"></span></li>
        </ul>

        <div class="modal-actions">
            <button id="modal-borrar-btn" class="btn btn-red">Borrar Reporte</button>
        </div>
    </div>
</div>

    <script src="/public/js/adminReportes.js"></script>

<?php include("views/partials/footer.php"); ?>