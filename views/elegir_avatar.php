<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/elegir_avatar.css">

<?php
$usuario = $_SESSION['usuario'] ?? null;
$avatares = [];
for ($i = 1; $i <= 16; $i++) {
    $num = str_pad($i, 2, "0", STR_PAD_LEFT);
    $avatares[] = "avatar{$num}.JPG";
}

$desdePerfil = !empty($_SESSION['permitir_configuracion']);
?>

<h2>Elige tu avatar <?= $desdePerfil ? "" : htmlspecialchars($usuario['nombre_usuario']) ?> 🎮</h2>
<p class="mensaje-usuario">
    <?= $desdePerfil ? "Selecciona uno para actualizar tu avatar" : "Selecciona uno para continuar al inicio de sesión" ?>
</p>

<form action="/trabajoPreguntasYRespuestas/<?= $desdePerfil ? 'usuario/elegirAvatar' : 'login/guardarAvatar' ?>" method="POST">
    <?php if (!$desdePerfil): ?>
        <input type="hidden" name="nombre_usuario" value="<?= htmlspecialchars($usuario['nombre_usuario'] ?? '') ?>">
    <?php endif; ?>
    <input type="hidden" name="foto_perfil" id="foto_perfil">

    <div class="avatar-container">
        <?php foreach ($avatares as $avatar): ?>
            <div class="avatar" data-avatar="/trabajoPreguntasYRespuestas/public/img/<?= $avatar ?>">
                <img src="/trabajoPreguntasYRespuestas/public/img/<?= $avatar ?>" alt="Avatar">
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" class="btn-confirmar" disabled id="btnConfirmar">Confirmar selección</button>
</form>

<script src="/trabajoPreguntasYRespuestas/public/js/elegir_avatar.js" defer></script>

<?php include("views/partials/footer.php"); ?>
