<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="public/css/elegir_avatar.css">

<?php
$nombre_usuario = $_GET["usuario"] ?? "";
$avatares = [];
for ($i = 1; $i <= 16; $i++) {
    $num = str_pad($i, 2, "0", STR_PAD_LEFT);
    $avatares[] = "avatar{$num}.JPG";
}
?>

<h2>Elige tu avatar, <?= htmlspecialchars($nombre_usuario) ?> 🎮</h2>
<p class="mensaje-usuario">Selecciona uno para continuar al inicio de sesión</p>

<form action="index.php?controller=LoginController&method=guardarAvatar" method="POST">
    <input type="hidden" name="nombre_usuario" value="<?= htmlspecialchars($nombre_usuario) ?>">
    <input type="hidden" name="foto_perfil" id="foto_perfil">

    <div class="avatar-container">
        <?php foreach ($avatares as $avatar): ?>
            <div class="avatar" data-avatar="public/img/<?= $avatar ?>">
                <img src="public/img/<?= $avatar ?>" alt="Avatar">
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn-confirmar" disabled id="btnConfirmar">Confirmar selección</button>
</form>

<script src="public/js/elegir_avatar.js" defer></script>

<?php include("views/partials/footer.php"); ?>