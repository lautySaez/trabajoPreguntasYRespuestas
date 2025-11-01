<?php include("views/partials/header-nologin.php"); ?>
<link rel="stylesheet" href="public/css/estilos.css">
<h2>Registro de nuevo usuario</h2>

<?php if (isset($error)): ?>
    <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="index.php?controller=LoginController&method=validarRegistrarUsuario" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <p>Por favor ingrese el codigo de verificación que se le envio a su casilla de correo:</p>
    </div>
    <br>
    <label>Nombre de usuario:</label>
    <input type="text" name="nombre_usuario" required><br>

    <label>Contraseña:</label>
    <input type="password" name="password" required><br>
    <div class="form-group">
        <label>Código de verificación:</label>
        <input type="number" name="token" required>
    </div>
        <br>
    <div class="form-group full-width">
        <button type="submit">Validar</button>
    </div>

</form>

<?php include("views/partials/footer.php"); ?>