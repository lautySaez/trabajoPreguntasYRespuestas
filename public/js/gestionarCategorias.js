function abrirModalCategoria(id, nombre, count) {
    // 1. Asigna el ID de la categoría a un campo oculto del formulario de eliminación.
    const idInput = document.getElementById('categoriaIdModal');
    if (idInput) {
        idInput.value = id;
    }

    const nombreDisplay = document.getElementById('nombreCategoriaModal');
    if (nombreDisplay) {
        nombreDisplay.textContent = nombre;
    }

    const preguntasDisplay = document.getElementById('preguntasAfectadasModal');
    if (preguntasDisplay) {
        const preguntasTexto = count + (count === '1' ? ' pregunta' : ' preguntas');
        preguntasDisplay.textContent = preguntasTexto;
    }

    const modal = document.getElementById('modalEliminacionCategoria');
    if (modal) {
        modal.style.display = 'block';
    }
}

function cerrarModalCategoria() {
    const modal = document.getElementById('modalEliminacionCategoria');
    if (modal) {
        modal.style.display = 'none';
    }
}

window.addEventListener('click', (event) => {
    const modal = document.getElementById('modalEliminacionCategoria');
    if (modal && event.target === modal) {
        cerrarModalCategoria();
    }
});

window.abrirModalCategoria = abrirModalCategoria;
window.cerrarModalCategoria = cerrarModalCategoria;