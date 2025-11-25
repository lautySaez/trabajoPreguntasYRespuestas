document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalDetalle');
    const closeBtn = document.getElementById('modal-close-btn');
    const closeX = document.querySelector('#modalDetalle .close-button');

    document.querySelectorAll('.ver-detalle').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('m_pregunta').innerText = btn.dataset.pregunta;
            document.getElementById('m_r1').innerText = btn.dataset.r1;
            document.getElementById('m_r2').innerText = btn.dataset.r2;
            document.getElementById('m_r3').innerText = btn.dataset.r3;
            document.getElementById('m_r4').innerText = btn.dataset.r4;
            document.getElementById('m_correcta').innerText = btn.dataset.correcta;
            document.getElementById('m_motivo').innerText = btn.dataset.motivo;

            modal.style.display = "flex";
        });
    });

    function cerrarModal() {
        modal.style.display = "none";
    }

    closeBtn.addEventListener('click', cerrarModal);
    closeX.addEventListener('click', cerrarModal);

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            cerrarModal();
        }
    });
});