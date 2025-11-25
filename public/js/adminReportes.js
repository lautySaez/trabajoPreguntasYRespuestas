document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('detalleModal');
    const closeBtn = document.querySelector('.close-button');
    const borrarBtn = document.getElementById('modal-borrar-btn');

    document.querySelectorAll('.btn-ver-mas').forEach(button => {
        button.addEventListener('click', async (e) => {
            const reporteId = e.currentTarget.getAttribute('data-id');
            const url = `index.php?controller=admin&method=obtenerDetalleReporte&id=${reporteId}`;

            try {
                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const detalle = await response.json();

                document.getElementById('reporte-id-display').textContent = detalle.id;
                document.getElementById('detalle-motivo').textContent = detalle.motivo;
                document.getElementById('detalle-fecha').textContent = detalle.fecha_reporte;
                document.getElementById('detalle-usuario').textContent = detalle.usuario_nombre;
                document.getElementById('detalle-email').textContent = detalle.usuario_email;
                document.getElementById('detalle-pregunta').textContent = detalle.pregunta_texto;

                document.getElementById('res1').textContent = detalle.respuesta1;
                document.getElementById('res2').textContent = detalle.respuesta2;
                document.getElementById('res3').textContent = detalle.respuesta3;
                document.getElementById('res4').textContent = detalle.respuesta4;
                document.getElementById('res-correcta').textContent = detalle.respuesta_correcta;

                borrarBtn.onclick = () => {
                    if (confirm(`¿Está seguro de borrar el reporte #${detalle.id} de manera permanente?`)) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'index.php?controller=admin&method=accionReporte';

                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id';
                        idInput.value = detalle.id;
                        form.appendChild(idInput);

                        const accionInput = document.createElement('input');
                        accionInput.type = 'hidden';
                        accionInput.name = 'accion';
                        accionInput.value = 'eliminar';
                        form.appendChild(accionInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                };

                modal.style.display = 'flex';

            } catch (error) {
                console.error("Error al cargar el detalle del reporte:", error);
                alert('Error al cargar el detalle. Verifique la consola para más información.');
            }
        });
    });

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});