<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="public/css/home.css">

<?php
$usuario = $_SESSION["usuario"] ?? null;
?>

<div class="home-container">
    <?php if ($usuario): ?>
        <h2>Bienvenido, <?= htmlspecialchars($usuario["nombre"]) ?>!</h2>
        <p>Tu rol: <?= htmlspecialchars($usuario["rol"] ?? "jugador") ?></p>
        <p>Email: <?= htmlspecialchars($usuario["email"]) ?></p>

        <?php if (!empty($usuario["foto_perfil"])): ?>
            <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" alt="Foto de perfil" class="foto-perfil">
        <?php endif; ?>

        <a href="index.php?controller=partida&method=mostrarReglas" class="boton-partida">Iniciar una Partida</a>

    <?php else: ?>
        <p>Error: no se encontró información del usuario.</p>
    <?php endif; ?>
</div>

    <script src="public/js/home.js" defer></script>

<?php include("views/partials/footer.php"); ?>