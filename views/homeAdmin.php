<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/homeAdmin.css">

<?php
require_once("helper/VerificacionDeRoles.php");
verificarRol("Administrador");
?>
    <h1>Panel del Administrador</h1>
 
<div class="admin-container">
    <?php if ($usuario && $usuario["rol"] === "Administrador"): ?>
        <h2>Bienvenido <?= htmlspecialchars($usuario["nombre"]) ?>!</h2>
        <p>Tu email: <?= htmlspecialchars($usuario["email"]) ?></p>

        <?php if (!empty($usuario["foto_perfil"])): ?>
            <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" alt="Foto de perfil" class="foto-perfil">
        <?php endif; ?>
        <br>
        <h3>Usuarios Registrados:</h3>
        <table>
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Nombre de Usuario</th>
                    <th>Email</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td style="text-align:center;">
                                <?php if (!empty($u["foto_perfil"])): ?>
                                    <img src="<?= htmlspecialchars($u["foto_perfil"]) ?>" alt="Avatar" width="50" height="50" style="border-radius:50%;">
                                <?php else: ?>
                                    <div style="
                                        width:50px;
                                        height:50px;
                                        border-radius:50%;
                                        background-color:#ccc;
                                        display:inline-block;
                                    "></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($u["nombre_usuario"]) ?></td>
                            <td><?= htmlspecialchars($u["email"]) ?></td>
                            <td><?= htmlspecialchars($u["estado_registro"] ?? 'Desconocido') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No hay usuarios registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>Error: no se encontró información del administrador.</p>
    <?php endif; ?>
</div>

<?php include("views/partials/footer.php"); ?>
