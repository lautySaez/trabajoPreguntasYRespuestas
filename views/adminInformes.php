<?php include("views/partials/header.php"); ?>

<?php
/** @var array $informes */
?>

    <link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/adminInformes.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;
if (!$usuario) {
    header("Location: /trabajoPreguntasYRespuestas/login");
    exit;
}
?>

    <div class="admin-app">

        <main class="admin-main">

            <header class="admin-topbar">
                <h1>Informes de Editores</h1>
            </header>

            <div class="card full">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Editor</th>
                        <th>Motivo</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($informes as $inf): ?>
                        <tr>
                            <td><?= $inf['id'] ?></td>
                            <td><?= htmlspecialchars($inf['tipo_accion']) ?></td>
                            <td><?= htmlspecialchars($inf['editor_nombre'] ?? "Sistema") ?></td>
                            <td><?= htmlspecialchars(substr($inf['motivo'], 0, 60)) ?>...</td>
                            <td><?= $inf['fecha'] ?></td>
                            <td>
                                <button class="btn btn-blue ver-detalle"
                                        data-id="<?= $inf['id'] ?>"
                                        data-pregunta="<?= htmlspecialchars($inf['pregunta_texto']) ?>"
                                        data-r1="<?= htmlspecialchars($inf['r1']) ?>"
                                        data-r2="<?= htmlspecialchars($inf['r2']) ?>"
                                        data-r3="<?= htmlspecialchars($inf['r3']) ?>"
                                        data-r4="<?= htmlspecialchars($inf['r4']) ?>"
                                        data-correcta="<?= htmlspecialchars($inf['correcta']) ?>"
                                        data-motivo="<?= htmlspecialchars($inf['motivo']) ?>">
                                    Ver detalles
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="back-link-container" style="text-align: right; margin-top: 20px;">
                <a href="/trabajoPreguntasYRespuestas/admin/homeAdmin" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
            </div>
        </main>
    </div>

    <div id="modalDetalle" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Detalle del Informe</h2>
            <p><strong>Pregunta:</strong> <span id="m_pregunta"></span></p>
            <p><strong>Respuestas:</strong></p>
            <ul>
                <li id="m_r1"></li>
                <li id="m_r2"></li>
                <li id="m_r3"></li>
                <li id="m_r4"></li>
            </ul>
            <p><strong>Correcta:</strong> <span id="m_correcta"></span></p>
            <p><strong>Motivo:</strong> <span id="m_motivo"></span></p>

            <button id="modal-close-btn" class="btn btn-secondary">Cerrar</button>
        </div>
    </div>

    <script src="/trabajoPreguntasYRespuestas/public/js/adminInformes.js"></script>

<?php include("views/partials/footer.php"); ?>