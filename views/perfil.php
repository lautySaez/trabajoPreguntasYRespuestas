<?php 
include("views/partials/header.php"); 

$usuario = $_SESSION["usuario"] ?? null;

if (!$usuario) {
    header("Location: index.php?controller=LoginController&method=inicioSesion");
    exit;
}
?>

<link rel="stylesheet" href="public/css/home.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<div class="home-container">
        <div class="welcome-section">
            <h2>Editar Perfil</h2>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="error-message" style="color: red; margin-bottom: 10px;">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']);?>
<?php endif; ?>

            <form action="index.php?controller=LoginController&method=actualizarPerfil" method="POST" enctype="multipart/form-data" class="perfil-form">

            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                <!-- Cambiar avatar -->
                <div class="form-group full-width avatar-section">
                    <a href="index.php?controller=LoginController&method=elegirAvatar">
                        <img src="<?= htmlspecialchars($usuario['foto_perfil'] ?: 'public/img/default_avatar.JPG') ?>" 
                            alt="Avatar Actual" 
                            class="avatar-actual" 
                            title="Hacé clic para cambiar tu avatar">
                    </a>
                </div>

                <!-- Cambiar username -->
                <div class="form-group full-width">
                    <label for="nombre_usuario">Nombre de Usuario:</label>
                    <input type="text" name="nombre_usuario" id="nombre_usuario" value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" required>
                </div>

                <!-- Cambiar email -->
                <div class="form-group full-width">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                </div>

                <!-- Cambiar pass -->
                <div class="form-group full-width">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" name="password" id="password" placeholder="Dejar vacío para no cambiar">
                </div>

                <div class="form-group full-width">
                    <label for="repassword">Repetir Contraseña:</label>
                    <input type="password" name="repassword" id="repassword" placeholder="Dejar vacío para no cambiar">
                </div>

                <div class="form-group full-width" style="text-align: center;">
                    <button type="submit" class="boton-partida">Actualizar Perfil</button>
                </div>
            </form>
        </div>
</div>

<?php include("views/partials/footer.php"); ?>