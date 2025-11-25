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