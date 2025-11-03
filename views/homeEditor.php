<?php include("views/partials/header.php"); ?>

<?php
$usuario = $_SESSION["usuario"] ?? null;
require_once("helper/VerificacionDeRoles.php");
verificarRol("Editor");
?>

<link rel="stylesheet" href="public/css/homeEditor.css">

<div class="editor-dashboard">
    <?php if ($usuario && $usuario["rol"] === "Editor"): ?>
        <div class="perfil-section">
            <h1>Editor</h1>
            <h2>Bienvenido <?= htmlspecialchars($usuario["nombre"]) ?>!</h2>

            <?php if (!empty($usuario["foto_perfil"])): ?>
                <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" alt="Foto de perfil" class="foto-perfil">
            <?php endif; ?>
        </div>

        <div class="cards-container">
            <a href="index.php?controller=editor&method=preguntasReportadas" class="card">
                <h3>Preguntas Reportadas</h3>
                <p>Revisá, editá o eliminá las preguntas que fueron reportadas por los usuarios.</p>
            </a>

            <a href="index.php?controller=editor&method=gestionarPreguntas" class="card">
                <h3>Gestión de Preguntas</h3>
                <p>Creá nuevas preguntas o editá las existentes del juego.</p>
            </a>
        </div>
    <?php else: ?>
        <p class="error-msg">Error: no se encontró información del editor.</p>
    <?php endif; ?>
</div>

<?php include("views/partials/footer.php"); ?>