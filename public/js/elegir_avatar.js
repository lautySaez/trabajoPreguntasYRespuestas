const avatars = document.querySelectorAll('.avatar');
const inputFoto = document.getElementById('foto_perfil');
const btnConfirmar = document.getElementById('btnConfirmar');

avatars.forEach(avatar => {
    avatar.addEventListener('click', () => {
        avatars.forEach(a => a.classList.remove('selected'));
        avatar.classList.add('selected');
        inputFoto.value = avatar.dataset.avatar;
        btnConfirmar.disabled = false;
    });
});