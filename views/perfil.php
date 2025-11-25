<?php
include("views/partials/header.php");
?>
    <link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/perfil.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$qr_api_url = '';

if (isset($_SESSION["usuario"])) {
    $usuario = $_SESSION["usuario"];

    if (isset($qr_url) && !empty($qr_url)) {
        $data_to_encode = urlencode($qr_url);

        $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . $data_to_encode;
    }
} else {
    header("Location: /trabajoPreguntasYRespuestas/login");
    exit;
}
?>

    <div class="contenedor-principal">

        <div class="perfil-card">
            <div class="avatar-container">
                <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?: 'public/img/default_avatar.JPG') ?>"
                     class="perfil-avatar" alt="Avatar">
            </div>

            <h2><?= htmlspecialchars($usuario['nombre_usuario']) ?></h2>
            <p class="email"><?= htmlspecialchars($usuario['email']) ?></p>
            <p class="password">******</p>

            <button id="abrirModal" class="boton-principal">Configurar perfil</button>
            <button id="abrirModalQR" class="boton-secundario" style="margin-top: 10px;">
                Compartir Perfil (QR)
            </button>

            <?php if (isset($datos_perfil)): ?>
                <div class="estadisticas-resumen" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                    <h4>Estadísticas</h4>
                    <p>Puntos Totales: <strong><?= number_format($datos_perfil['puntos_totales'] ?? 0, 0, ',', '.') ?></strong></p>
                    <p>Partidas Jugadas: <strong><?= number_format($datos_perfil['partidas_jugadas'] ?? 0, 0, ',', '.') ?></strong></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="modalPassword" class="modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button class="cerrar" id="cerrarModal" aria-label="Cerrar">&times;</button>
            <h3 id="modalTitle">Confirmar contraseña</h3>
            <p>Para editar tu perfil, ingresa tu contraseña actual:</p>

            <form id="formConfirmar" action="/trabajoPreguntasYRespuestas/usuario/confirmarPassword" method="POST">
                <input type="password" name="password_actual" placeholder="Contraseña actual" required autocomplete="current-password">
                <div class="modal-buttons">
                    <button type="submit" class="boton-principal">Continuar</button>
                    <button type="button" class="boton-secundario" id="cancelarModal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalQR" class="modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalQRTitle" style="text-align: center;">
            <button class="cerrar" id="cerrarModalQR" aria-label="Cerrar">&times;</button>
            <h3 id="modalQRTitle">Comparte tu Perfil</h3>
            <p>Escanea el código para ver las estadísticas públicas de <?= htmlspecialchars($usuario['nombre_usuario']) ?>.</p>

            <div class="qr-code-display" style="padding: 20px;">
                <?php if (!empty($qr_api_url)): ?>
                    <img src="<?= htmlspecialchars($qr_api_url) ?>" alt="Código QR de Perfil" />
                <?php else: ?>
                    <p>Error al generar el QR. La URL pública no está disponible.</p>
                <?php endif; ?>
            </div>
            <p style="font-size: 0.8em; color: #6c757d;">URL: <?= htmlspecialchars($qr_url ?? 'N/A') ?></p>

            <div class="modal-buttons">
                <button type="button" class="boton-secundario" id="cerrarModalQR2">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="/trabajoPreguntasYRespuestas/public/js/perfil.js" type="text/javascript"></script>

<?php include("views/partials/footer.php"); ?>