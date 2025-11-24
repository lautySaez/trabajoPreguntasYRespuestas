document.addEventListener('DOMContentLoaded', () => {
    const temporizador = document.getElementById('temporizador');
    if (!temporizador) return;

    let tiempo = 20;
    let expirado = false;

    const deshabilitarRespuestas = () => {
        document.querySelectorAll('.respuestas-grid form button').forEach(b => {
            b.disabled = true;
            b.classList.add('btn-disabled');
        });
    };

    const finalizarPorTiempo = () => {
        console.debug('[temporizador] Tiempo agotado: redirigiendo a feedback de tiempo');
        // Un solo redirect GET evita doble invocación que limpiaba la sesión antes de mostrar feedback
        window.location.href = 'index.php?controller=partida&method=tiempoAgotado&t=' + Date.now();
    };

    const countdown = setInterval(() => {
        temporizador.textContent = 'Tiempo restante: ' + tiempo + ' segundos';
        tiempo--;
        if (tiempo < 0 && !expirado) {
            expirado = true;
            clearInterval(countdown);
            temporizador.textContent = 'Tiempo agotado';
            deshabilitarRespuestas();
            console.debug('[temporizador] Disparando flujo de fin por tiempo');
            finalizarPorTiempo();
        }
    }, 1000);

    document.querySelectorAll('.respuestas-grid form').forEach(form => {
        form.addEventListener('submit', () => {
            clearInterval(countdown);
            console.debug('[temporizador] Respuesta enviada antes de timeout');
        });
    });
});
