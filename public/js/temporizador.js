document.addEventListener('DOMContentLoaded', () => {
    const temporizador = document.getElementById('temporizador');
    if (!temporizador) return;

    let tiempo = 10;
    let countdown = setInterval(() => {
        temporizador.textContent = 'Tiempo restante: ' + tiempo + ' segundos';
        tiempo--;

        if (tiempo < 0) {
            clearInterval(countdown);
            // redirigir auto sgte pregunta
            window.location.href = "index.php?controller=partida&method=siguientePregunta";
        }
    }, 1000);

    // detener timer si responde antes
    const botonesRespuesta = document.querySelectorAll('.respuestas-grid form');
    botonesRespuesta.forEach(form => {
        form.addEventListener('submit', () => {
            clearInterval(countdown);
        });
    });
});
