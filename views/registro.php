<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="public/css/registro.css">

<h2>Registro de nuevo usuario</h2>

<?php if (isset($error)): ?>
    <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form id="formRegistro" action="index.php?controller=LoginController&method=registrarUsuario" method="POST">

    <div class="form-group">
        <label>Nombre completo:</label>
        <input type="text" name="nombre" required>
    </div>

    <div class="form-group">
        <label>Fecha de nacimiento:</label>
        <input type="date" name="fecha_nacimiento" max="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="form-group">
        <label>Sexo:</label>
        <select name="sexo">
            <option>Masculino</option>
            <option>Femenino</option>
            <option>Prefiero no cargarlo</option>
        </select>
    </div>

    <div id="map" style="height: 150px; border-radius: 8px; margin-bottom: 20px;"></div>
    <input type="hidden" name="latitud" id="latitud">
    <input type="hidden" name="longitud" id="longitud">

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

    <!-- Eliminado campo de foto_perfil, ahora se elige luego -->

    <div class="form-group full-width" style="text-align:center;">
        <label>
            <input type="checkbox" id="aceptarTerminos"> Acepto los
            <a href="#" id="verTerminos">Términos y Condiciones</a>
        </label>
    </div>

    <div class="form-group full-width">
        <button type="submit" id="btnRegistrarme" disabled>Registrarme</button>
    </div>

</form>

<p>¿Ya tienes cuenta? <a href="index.php?controller=LoginController&method=inicioSesion">Inicia sesión</a></p>

<div id="modalTerminos" class="modal">
    <div class="modal-content">
        <h2>Términos y Condiciones</h2>
        <p>
            Al registrarte en esta aplicación, aceptas las políticas de uso, privacidad y condiciones del sistema de juego.
            La información proporcionada será utilizada únicamente con fines de autenticación y estadísticas del juego.
        </p>
        <p>
            No se compartirán tus datos con terceros sin tu consentimiento. Puedes eliminar tu cuenta en cualquier momento.
            El uso continuo implica la aceptación de futuras actualizaciones en los términos.
        </p>
        <div class="modal-buttons">
            <button id="btnAceptar">Aceptar</button>
            <button id="btnCerrar">Cerrar</button>
        </div>
    </div>
</div>

    <script src="public/js/registro.js" defer></script>


<?php include("views/partials/footer.php"); ?>