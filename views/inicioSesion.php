<?php include("views/partials/header.php"); ?>

<h2>Iniciar Sesión</h2>

<?php if (isset($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="index.php?controller=LoginController&method=login" method="POST">
    <label>Nombre de usuario:</label>
    <input type="text" name="nombre_usuario" required><br>

    <label>Contraseña:</label>
    <input type="password" name="password" required><br>

    <button type="submit">Ingresar</button>
</form>

<p>¿No tienes cuenta?
    <a href="index.php?controller=LoginController&method=registro">Registrarse</a>

    Regístrate aquí
    </a>
</p>

<?php include("views/partials/footer.php"); ?>


