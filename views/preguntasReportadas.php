<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/homeAdmin.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION["usuario"])) {
    $usuario = $_SESSION["usuario"];
} else {
    header("Location: index.php?controller=LoginController&method=inicioSesion");
    exit;
}

require_once("helper/VerificacionDeRoles.php");
verificarRol("Editor");
?>

<h1>Preguntas Reportadas</h1>

<div class="admin-container">
    <?php if (!empty($reportes)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Reporte</th>
                    <th>Pregunta</th>
                    <th>Usuario que reportó</th>
                    <th>Motivo</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportes as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r["id"]) ?></td>
                        <td><?= htmlspecialchars($r["pregunta"]) ?></td>
                        <td><?= htmlspecialchars($r["usuario"]) ?></td>
                        <td><?= htmlspecialchars($r["motivo"]) ?></td>
                        <td><?= htmlspecialchars($r["fecha_reporte"]) ?></td>
                        <td>
                            <?php if (isset($r["revisado"]) && $r["revisado"]): ?>
                                <span style="color: green; font-weight: bold;">Revisado</span>
                            <?php else: ?>
                                <span style="color: red; font-weight: bold;">Pendiente</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay preguntas reportadas por el momento.</p>
    <?php endif; ?>
</div>

<?php include("views/partials/footer.php"); ?>
