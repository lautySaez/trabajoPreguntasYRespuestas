document.addEventListener("DOMContentLoaded", () => {
    const avatar = document.querySelector(".perfil-avatar");

    if (avatar) {
        avatar.addEventListener("click", (e) => {
            e.preventDefault();

            // Efecto visual al hacer clic
            avatar.classList.add("avatar-clicked");
            setTimeout(() => {
                window.location.href = "index.php?controller=LoginController&method=elegirAvatar";
            }, 300);
        });
    }

    // Pequeño efecto al cargar los inputs
    const inputs = document.querySelectorAll("input");
    inputs.forEach(input => {
        input.addEventListener("focus", () => {
            input.style.transform = "scale(1.02)";
        });
        input.addEventListener("blur", () => {
            input.style.transform = "scale(1)";
        });
    });
});
