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

    <!-- Contenedor del mapa -->
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

    <div class="form-group">
        <label>Foto de perfil:</label>
        <input type="file" name="foto_perfil">
    </div>

    <div class="form-group full-width">
        <button type="submit">Registrarme</button>
    </div>

</form>

<p>¿Ya tienes cuenta? <a href="index.php?controller=LoginController&method=index">Inicia sesión</a></p>

<script>
    // Inicializa el mapa centrado en Argentina
    var map = L.map('map').setView([-34.61, -58.38], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([-34.61, -58.38], {draggable: true}).addTo(map);

    function updateCoordinates(lat, lng) {
        document.getElementById('latitud').value = lat;
        document.getElementById('longitud').value = lng;
    }

    marker.on('dragend', function (e) {
        var coords = e.target.getLatLng();
        updateCoordinates(coords.lat, coords.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });
</script>

<?php include("views/partials/footer.php"); ?>