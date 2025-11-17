<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/adminInfoYRepo.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) {
    header("Location: index.php?controller=LoginController&method=inicioSesion");
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
                            <form method="POST"
                                  action="index.php?controller=admin&method=accionReporte"
                                  onsubmit="return confirm('¿Eliminar este reporte?')">

                                <input type="hidden" name="id" value="<?= $rep['id'] ?>">
                                <input type="hidden" name="accion" value="eliminar">

                                <button class="btn btn-red">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

<?php include("views/partials/footer.php"); ?>
