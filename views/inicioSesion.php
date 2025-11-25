<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/inicioSesion.css">

<main class="login-container">
    <div class="login-card">
        <h2>Iniciar Sesión</h2>

        <?php if (isset($error)): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="/trabajoPreguntasYRespuestas/login" method="POST">
            <div class="form-group">
                <label for="nombre_usuario">Nombre de usuario</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Ingresa tu usuario" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Ingresar</button>
        </form>

        <p class="register-link">
            ¿No tienes cuenta?
            <a href="/trabajoPreguntasYRespuestas/login/registro">Regístrate</a>
        </p>
    </div>
</main>

<script src="/trabajoPreguntasYRespuestas/public/js/inicioSesion.js" defer></script>
<?php include("views/partials/footer.php"); ?>



