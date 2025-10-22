<?php include("views/partials/header.php"); ?>

<?php if (isset($usuario)): ?>
    <h2>Bienvenido, <?= htmlspecialchars($usuario["nombre"]) ?>!</h2>
    <p>Tu rol: <?= htmlspecialchars($usuario["rol"] ?? "jugador") ?></p>
    <p>Email: <?= htmlspecialchars($usuario["email"]) ?></p>

    <?php if (!empty($usuario["foto_perfil"])): ?>
        <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" width="150" alt="Foto de perfil">
    <?php endif; ?>

    <a href="index.php?controller=partida&method=mostrarReglas" class="boton-partida">Iniciar una Partida</a>

<?php else: ?>
    <p>Error: no se encontró información del usuario.</p>
<?php endif; ?>

<?php include("views/partials/footer.php"); ?>

