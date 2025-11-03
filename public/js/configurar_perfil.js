document.addEventListener("DOMContentLoaded", () => {
    const avatarLink = document.querySelector(".perfil-avatar").parentElement;

    if (avatarLink) {
        avatarLink.addEventListener("click", (e) => {
            e.preventDefault();

            const avatarImg = avatarLink.querySelector("img");
            avatarImg.classList.add("avatar-clicked");

            setTimeout(() => {
                window.location.href = avatarLink.href;
            }, 200);
        });
    }

    const inputs = document.querySelectorAll("input[type='text'], input[type='email'], input[type='password']");
    inputs.forEach(input => {
        input.addEventListener("focus", () => {
            input.style.transform = "scale(1.02)";
        });
        input.addEventListener("blur", () => {
            input.style.transform = "scale(1)";
        });
    });
});