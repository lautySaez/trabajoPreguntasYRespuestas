<?php include("views/partials/header.php"); ?>

<?php
require_once("helper/VerificacionDeRoles.php");
verificarRol("Administrador");
?>
    <h1>Panel del Administrador</h1>
$usuario = $_SESSION["usuario"] ?? null;
?>

<style>
    .admin-container {
        text-align: center;
        margin-top: 40px;
        color: #fff;
        font-family: 'Poppins', sans-serif;
    }

    .foto-perfil {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #4CAF50;
        box-shadow: 0 0 15px rgba(76, 175, 80, 0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: block;
        margin: 20px auto;
    }

    .foto-perfil:hover {
        transform: scale(1.1);
        box-shadow: 0 0 25px rgba(76, 175, 80, 0.6);
    }

    .boton-admin {
        display: inline-block;
        background: #4CAF50;
        color: #fff;
        text-decoration: none;
        padding: 12px 25px;
        border-radius: 25px;
        margin-top: 20px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .boton-admin:hover {
        background: #45a049;
        transform: scale(1.05);
    }
</style>

<div class="admin-container">
    <?php if ($usuario && $usuario["rol"] === "admin"): ?>
        <h2>Bienvenido Administrador, <?= htmlspecialchars($usuario["nombre"]) ?>!</h2>
        <p>Email: <?= htmlspecialchars($usuario["email"]) ?></p>

        <?php if (!empty($usuario["foto_perfil"])): ?>
            <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" alt="Foto de perfil" class="foto-perfil">
        <?php endif; ?>

        <a href="index.php?controller=admin&method=dashboard" class="boton-admin">Ir al Panel de Administración</a>

    <?php else: ?>
        <p>Error: no se encontró información del administrador.</p>
    <?php endif; ?>
</div>

<?php include("views/partials/footer.php"); ?>
