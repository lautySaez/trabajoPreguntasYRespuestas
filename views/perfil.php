<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/perfil.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION["usuario"])) {
    $usuario = $_SESSION["usuario"];
} else {
    header("Location: index.php?controller=LoginController&method=inicioSesion");
    exit;
}
?>

<div class="perfil-container">
    <h2>Mi Perfil</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="perfil-info">
        <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?: 'public/img/default_avatar.JPG') ?>" class="perfil-avatar" alt="Avatar">

        <p><strong>Nombre de usuario:</strong> <?= htmlspecialchars($usuario['nombre_usuario']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
        <p><strong>Contraseña:</strong> ******</p>

        <button id="abrirModal" class="boton-partida">Configurar perfil</button>
    </div>
</div>

<div id="modalPassword" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <button class="cerrar" id="cerrarModal" aria-label="Cerrar">&times;</button>
        <h3 id="modalTitle">Confirmar contraseña</h3>
        <p>Para editar tu perfil, ingresa tu contraseña actual:</p>

        <form id="formConfirmar" action="index.php?controller=UsuarioController&method=confirmarPassword" method="POST">
            <input type="password" name="password_actual" placeholder="Contraseña actual" required autocomplete="current-password">
            <div style="margin-top:12px;">
                <button type="submit" class="boton-partida">Continuar</button>
                <button type="button" class="boton-secundario" id="cancelarModal">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="public/js/perfil.js" type="text/javascript"></script>

<?php include("views/partials/footer.php"); ?>