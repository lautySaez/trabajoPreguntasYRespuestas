<?php include("views/partials/header.php"); ?>

<?php
$usuario = $_SESSION["usuario"] ?? null;
?>

<style>
    .editor-container {
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
        border: 4px solid #2196F3; /* color azul para distinguir editor */
        box-shadow: 0 0 15px rgba(33, 150, 243, 0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: block;
        margin: 20px auto;
    }

    .foto-perfil:hover {
        transform: scale(1.1);
        box-shadow: 0 0 25px rgba(33, 150, 243, 0.6);
    }

    .boton-editor {
        display: inline-block;
        background: #2196F3;
        color: #fff;
        text-decoration: none;
        padding: 12px 25px;
        border-radius: 25px;
        margin-top: 20px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .boton-editor:hover {
        background: #1976D2;
        transform: scale(1.05);
    }
</style>

<div class="editor-container">
    <?php if ($usuario && $usuario["rol"] === "editor"): ?>
        <h2>Bienvenido Editor, <?= htmlspecialchars($usuario["nombre"]) ?>!</h2>
        <p>Email: <?= htmlspecialchars($usuario["email"]) ?></p>

        <?php if (!empty($usuario["foto_perfil"])): ?>
            <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" alt="Foto de perfil" class="foto-perfil">
        <?php endif; ?>

        <a href="index.php?controller=editor&method=dashboard" class="boton-editor">Ir al Panel de Editor</a>

    <?php else: ?>
        <p>Error: no se encontró información del editor.</p>
    <?php endif; ?>
</div>

<?php include("views/partials/footer.php"); ?>
