document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById("modalAccion");
    var tipoAccion = document.getElementById("tipoAccion");
    var preguntaId = document.getElementById("preguntaId");
    var formAccion = document.getElementById("formAccion");
    var formDataInput = document.getElementById("formData");

    window.habilitarEdicion = function(id) {
        var fila = document.getElementById("pregunta-" + id);
        if (!fila) return;

        var spans = fila.querySelectorAll("[data-field]");
        for (var i = 0; i < spans.length; i++) {
            var el = spans[i];
            var campo = el.getAttribute("data-field");
            var valor = el.textContent.trim();

            if (["pregunta", "r1", "r2", "r3", "r4", "correcta"].indexOf(campo) !== -1) {
                el.innerHTML = '<input type="text" name="' + campo + '" value="' + valor + '"/>';
            }
        }

        var acciones = fila.querySelector(".acciones");
        acciones.innerHTML =
            '<button class="btn-confirmar" onclick="abrirModal(\'edicion\', ' + id + ')">Confirmar</button>' +
            '<button class="btn-cancelar" onclick="window.location.reload()">Cancelar</button>';
    };

    window.abrirModal = function(tipo, id) {
        tipoAccion.value = tipo;
        preguntaId.value = id;

        if (tipo === "edicion") {
            var fila = document.getElementById("pregunta-" + id);
            var inputs = fila.querySelectorAll("input");
            var data = {};
            for (var i = 0; i < inputs.length; i++) {
                data[inputs[i].name] = inputs[i].value;
            }
            formDataInput.value = JSON.stringify(data);
            formAccion.action = "index.php?controller=editor&method=editarPregunta";
            document.getElementById("modalTitulo").innerText = "Informe de edición";
        } else {
            formAccion.action = "index.php?controller=editor&method=borrarPregunta";
            document.getElementById("modalTitulo").innerText = "Informe de eliminación";
        }

        modal.style.display = "block";
    };

    window.cerrarModal = function() {
        modal.style.display = "none";
    };

    window.onclick = function(e) {
        if (e.target === modal) modal.style.display = "none";
    };
});