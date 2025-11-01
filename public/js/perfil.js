document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modalPassword");
    const abrir = document.getElementById("abrirModal");
    const cerrar = document.getElementById("cerrarModal");
    const cancelar = document.getElementById("cancelarModal");

    if (!modal || !abrir) return;

    function abrirModal() {
        modal.style.display = "flex";
        modal.setAttribute("aria-hidden", "false");

        const input = modal.querySelector('input[name="password_actual"]');
        if (input) input.focus();
    }

    function cerrarModal() {
        modal.style.display = "none";
        modal.setAttribute("aria-hidden", "true");
    }

    abrir.addEventListener("click", abrirModal);
    if (cerrar) cerrar.addEventListener("click", cerrarModal);
    if (cancelar) cancelar.addEventListener("click", cerrarModal);

    window.addEventListener("click", function (e) {
        if (e.target === modal) cerrarModal();
    });

    window.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && modal.style.display === "flex") cerrarModal();
    });
});

