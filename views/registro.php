<?php include("views/partials/header.php"); ?>

<h2>Registro de nuevo usuario</h2>

<?php if (isset($error)): ?>
    <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="index.php?controller=LoginController&method=registrarUsuario" method="POST" enctype="multipart/form-data">

    <div class="form-group">
        <label>Nombre completo:</label>
        <input type="text" name="nombre" required>
    </div>

    <div class="form-group">
        <label>Año de nacimiento:</label>
        <input type="number" name="anio_nacimiento" min="1900" max="<?= date("Y") ?>">
    </div>

    <div class="form-group">
        <label>Sexo:</label>
        <select name="sexo">
            <option>Masculino</option>
            <option>Femenino</option>
            <option>Prefiero no cargarlo</option>
        </select>
    </div>

    <div class="form-group">
        <label>País:</label>
        <input type="text" name="pais">
    </div>

    <div class="form-group">
        <label>Ciudad:</label>
        <input type="text" name="ciudad">
    </div>

    <div class="form-group">
        <label>Email:</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Nombre de usuario:</label>
        <input type="text" name="nombre_usuario" required>
    </div>

    <div class="form-group">
        <label>Contraseña:</label>
        <input type="password" name="password" required>
    </div>

    <div class="form-group">
        <label>Repetir contraseña:</label>
        <input type="password" name="repassword" required>
    </div>

    <div class="form-group">
        <label>Foto de perfil:</label>
        <input type="file" name="foto_perfil">
    </div>

    <div class="form-group full-width">
        <button type="submit">Registrarme</button>
    </div>

</form>

<p>¿Ya tienes cuenta? <a href="index.php?controller=LoginController&method=index">Inicia sesión</a></p>

<?php include("views/partials/footer.php"); ?>