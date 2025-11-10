<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/preguntasReportadas.css">

<h1>Preguntas Reportadas</h1>

<?php if (!empty($reportes)): ?>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
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
                    <td><?= $r["revisado"] ? "Revisado" : "Pendiente" ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay preguntas reportadas por el momento.</p>
<?php endif; ?>

<?php include("views/partials/footer.php"); ?>
