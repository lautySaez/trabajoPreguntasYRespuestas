<?php include("views/partials/header.php"); ?>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION["usuario"])) {
    $usuario = $_SESSION["usuario"];
} else {
    header("Location: /trabajoPreguntasYRespuestas/login");
    exit;
}
?>
<link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/configurar_perfil.css">

<div class="contenedor-principal">
    <div class="perfil-card">
        <h2>Configurar Perfil</h2>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="/trabajoPreguntasYRespuestas/usuario/actualizarPerfil"
              method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label for="nombre_usuario">Nombre de usuario:</label>
                <input type="text" name="nombre_usuario"
                       value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Nueva contraseña:</label>
                <input type="password" name="password" placeholder="Dejar vacío para no cambiar">
            </div>

            <div class="form-group">
                <label for="repassword">Repetir contraseña:</label>
                <input type="password" name="repassword" placeholder="Dejar vacío para no cambiar">
            </div>

            <div class="form-group avatar-group">
                <label>Avatar actual:</label><br>
                <a href="/trabajoPreguntasYRespuestas/usuario/elegirAvatar">
                    <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?: 'public/img/default_avatar.JPG') ?>"
                         class="perfil-avatar" title="Cambiar avatar">
                </a>
            </div>

            <button type="submit" class="boton-principal">Guardar Cambios</button>
        </form>
    </div>
</div>

<script src="/trabajoPreguntasYRespuestas/public/js/configurar_perfil.js" defer></script>

<?php include("views/partials/footer.php"); ?>
