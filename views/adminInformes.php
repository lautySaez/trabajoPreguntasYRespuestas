<?php include("views/partials/header.php"); ?>

<?php
/** @var array $informes */
?>

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

    </main>
</div>

<div id="modalDetalle" class="modal" style="display:none;">
    <div class="modal-content">
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

        <button onclick="cerrarModal()" class="btn btn-red">Cerrar</button>
    </div>
</div>

<script>
    document.querySelectorAll('.ver-detalle').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('m_pregunta').innerText = btn.dataset.pregunta;
            document.getElementById('m_r1').innerText = btn.dataset.r1;
            document.getElementById('m_r2').innerText = btn.dataset.r2;
            document.getElementById('m_r3').innerText = btn.dataset.r3;
            document.getElementById('m_r4').innerText = btn.dataset.r4;
            document.getElementById('m_correcta').innerText = btn.dataset.correcta;
            document.getElementById('m_motivo').innerText = btn.dataset.motivo;

            document.getElementById('modalDetalle').style.display = "flex";
        });
    });

    function cerrarModal(){
        document.getElementById('modalDetalle').style.display = "none";
    }
</script>

<?php include("views/partials/footer.php"); ?>
