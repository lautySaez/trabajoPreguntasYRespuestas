document.addEventListener('DOMContentLoaded', () => {
    const temporizador = document.getElementById('temporizador');
    if (!temporizador) return;

    let tiempo = 10;
    let countdown = setInterval(() => {
        temporizador.textContent = 'Tiempo restante: ' + tiempo + ' segundos';
        tiempo--;

        if (tiempo < 0) {
            clearInterval(countdown);
            document.getElementById("form-timeout").submit();
        }
    }, 1000);

    const botonesRespuesta = document.querySelectorAll('.respuestas-grid form');
    botonesRespuesta.forEach(form => {
        form.addEventListener('submit', () => {
            clearInterval(countdown);
        });
    });
});
