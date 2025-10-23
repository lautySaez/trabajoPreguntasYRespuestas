<?php include("views/partials/header.php"); ?>

<h2>Registro de nuevo usuario</h2>

<?php if (isset($error)): ?>
    <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form id="formRegistro" action="index.php?controller=LoginController&method=registrarUsuario" method="POST" enctype="multipart/form-data">

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

    <div class="form-group">
        <label>Foto de perfil:</label>
        <input type="file" name="foto_perfil">
    </div>

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

<p>¿Ya tienes cuenta? <a href="index.php?controller=LoginController&method=index">Inicia sesión</a></p>

<div id="modalTerminos" class="modal">
    <div class="modal-content">
        <h2>Términos y Condiciones</h2>
        <p>
            Al registrarte en esta aplicación, aceptas las políticas de uso, privacidad y condiciones del sistema de juego.
            La información proporcionada será utilizada únicamente con fines de autenticación y estadísticas del juego.
            Nos reservamos el derecho de suspender cuentas que infrinjan las normas o hagan uso indebido del sistema.
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

<script>

    var map = L.map('map').setView([-34.61, -58.38], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([-34.61, -58.38], { draggable: true }).addTo(map);

    function updateCoordinates(lat, lng) {
        document.getElementById('latitud').value = lat;
        document.getElementById('longitud').value = lng;
        getProvince(lat, lng);
    }

    function getProvince(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=es`)
            .then(response => response.json())
            .then(data => {
                if (data && data.address) {
                    const address = data.address;
                    const country = address.country || '';
                    let provincia = address.state || address.region || '';

                    if (provincia.toLowerCase().includes('buenos aires')) {
                        if (
                            (address.city && address.city.toLowerCase().includes('buenos aires')) ||
                            provincia.toLowerCase().includes('autónoma') ||
                            provincia.toLowerCase().includes('autonoma')
                        ) {
                            provincia = 'Capital Federal';
                        } else {
                            provincia = 'Provincia de Buenos Aires';
                        }
                    }

                    document.querySelector('input[name="pais"]').value = country;
                    document.querySelector('input[name="ciudad"]').value = provincia;
                }
            })
            .catch(err => console.error('Error al obtener la ubicación:', err));
    }

    marker.on('dragend', e => {
        const coords = e.target.getLatLng();
        updateCoordinates(coords.lat, coords.lng);
    });

    map.on('click', e => {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    updateCoordinates(-34.61, -58.38);

    const modal = document.getElementById('modalTerminos');
    const verTerminos = document.getElementById('verTerminos');
    const btnCerrar = document.getElementById('btnCerrar');
    const btnAceptar = document.getElementById('btnAceptar');
    const checkTerminos = document.getElementById('aceptarTerminos');
    const btnRegistrarme = document.getElementById('btnRegistrarme');

    verTerminos.addEventListener('click', e => {
        e.preventDefault();
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });

    btnCerrar.addEventListener('click', () => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    btnAceptar.addEventListener('click', () => {
        checkTerminos.checked = true;
        btnRegistrarme.disabled = false;
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    checkTerminos.addEventListener('change', () => {
        btnRegistrarme.disabled = !checkTerminos.checked;
    });
</script>

<?php include("views/partials/footer.php"); ?>