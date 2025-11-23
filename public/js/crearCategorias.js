document.addEventListener('DOMContentLoaded', () => {

    const preguntasBoxes = document.querySelectorAll('.pregunta-box');

    preguntasBoxes.forEach((box, index) => {
        const i = index + 1;

        const inputsRespuestas = [
            box.querySelector(`input[name="r${i}_1"]`),
            box.querySelector(`input[name="r${i}_2"]`),
            box.querySelector(`input[name="r${i}_3"]`),
            box.querySelector(`input[name="r${i}_4"]`)
        ];

        const gridContainer = document.createElement('div');
        gridContainer.classList.add('respuestas-grid');

        const labelCorrecta = box.querySelector(`label[for="correcta_${i}"]`);

        const labelTituloRespuestas = box.querySelector('label:nth-of-type(2)');

        if(labelTituloRespuestas) {
            box.insertBefore(gridContainer, labelCorrecta);
            inputsRespuestas.forEach(input => {
                if(input) gridContainer.appendChild(input);
            });
        }

        const inputCorrectaNumber = box.querySelector(`input[name="correcta_${i}"]`);

        if (inputCorrectaNumber) {
            const updateHighlight = () => {
                inputsRespuestas.forEach(input => {
                    if(input) input.classList.remove('respuesta-correcta-highlight');
                });

                const valor = parseInt(inputCorrectaNumber.value);

                if (valor >= 1 && valor <= 4) {
                    const targetInput = inputsRespuestas[valor - 1];
                    if (targetInput) {
                        targetInput.classList.add('respuesta-correcta-highlight');
                    }
                }
            };

            inputCorrectaNumber.addEventListener('input', updateHighlight);
            inputCorrectaNumber.addEventListener('change', updateHighlight);
        }
    });

    console.log('Script de Crear Categoría cargado: Grid y Highlight activos.');
});