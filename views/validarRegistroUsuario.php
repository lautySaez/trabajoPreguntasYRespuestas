<?php include("views/partials/header-nologin.php"); ?>

<link rel="stylesheet" href="public/css/validarRegistroUsuario.css">

<main class="register-container">
    <div class="register-card">
        <h2>Registro de nuevo usuario</h2>

        <?php if (isset($error)): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="index.php?controller=LoginController&method=validarRegistrarUsuario" method="POST" enctype="multipart/form-data">
            <p class="info-text">Por favor ingrese el código de verificación que se le envió a su casilla de correo:</p>

            <div class="form-group">
                <label for="nombre_usuario">Nombre de usuario</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Ingresa tu usuario" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="token">Código de verificación</label>
                <input type="number" id="token" name="token" placeholder="Ingresa el código" required>
            </div>

            <div class="form-group full-width centered">
                <button type="submit" class="btn-register">Validar</button>
            </div>
        </form>
    </div>
</main>

<?php include("views/partials/footer.php"); ?>