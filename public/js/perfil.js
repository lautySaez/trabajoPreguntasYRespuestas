document.addEventListener("DOMContentLoaded", function () {
    const modalPassword = document.getElementById("modalPassword");
    const modalQR = document.getElementById("modalQR"); // NUEVO MODAL QR
    const abrirModalPassword = document.getElementById("abrirModal");
    const cerrarModalPassword = document.getElementById("cerrarModal");
    const cancelarModalPassword = document.getElementById("cancelarModal");
    const abrirModalQR = document.getElementById("abrirModalQR"); // NUEVO BOTÓN
    const cerrarModalQR = document.getElementById("cerrarModalQR");
    const cerrarModalQR2 = document.getElementById("cerrarModalQR2"); // Botón 'Cerrar' dentro del modal

    function abrirModal(modalElement) {
        if (!modalElement) return;
        modalElement.style.display = "flex";
        modalElement.setAttribute("aria-hidden", "false");

        if (modalElement === modalPassword) {
            const input = modalElement.querySelector('input[name="password_actual"]');
            if (input) input.focus();
        }
    }

    function cerrarModal(modalElement) {
        if (!modalElement) return;
        modalElement.style.display = "none";
        modalElement.setAttribute("aria-hidden", "true");
    }
    if (abrirModalPassword) abrirModalPassword.addEventListener("click", () => abrirModal(modalPassword));
    if (cerrarModalPassword) cerrarModalPassword.addEventListener("click", () => cerrarModal(modalPassword));
    if (cancelarModalPassword) cancelarModalPassword.addEventListener("click", () => cerrarModal(modalPassword));
    if (abrirModalQR) abrirModalQR.addEventListener("click", () => abrirModal(modalQR));
    if (cerrarModalQR) cerrarModalQR.addEventListener("click", () => cerrarModal(modalQR));
    if (cerrarModalQR2) cerrarModalQR2.addEventListener("click", () => cerrarModal(modalQR));

    window.addEventListener("click", (e) => {
        if (e.target === modalPassword) cerrarModal(modalPassword);
        if (e.target === modalQR) cerrarModal(modalQR);
    });

    window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            if (modalPassword && modalPassword.style.display === "flex") {
                cerrarModal(modalPassword);
            }
            if (modalQR && modalQR.style.display === "flex") {
                cerrarModal(modalQR);
            }
        }
    });
});