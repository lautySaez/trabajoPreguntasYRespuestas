<?php
include(__DIR__ . "/../views/partials/header.php");
?>

    <link rel="stylesheet" href="/trabajoPreguntasYRespuestas/public/css/adminUsuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    header("Location: /trabajoPreguntasYRespuestas/login");
    exit;
}
?>

    <div class="content">
        <div class="topbar">
            <h1>Gestión de Jugadores</h1>
        </div>

        <div class="action-bar">
            <h3>Lista de Usuarios</h3>

            <div class="filter-group">
                <input type="text" id="filter-search" placeholder="Buscar por ID, Usuario o Email">

                <select id="filter-role">
                    <option value="">-- Rol --</option>
                    <option value="Jugador">Jugador</option>
                    <option value="Editor">Editor</option>
                </select>

                <select id="filter-status">
                    <option value="">-- Estado --</option>
                    <option value="Activo">Activo</option>
                    <option value="Bloqueado">Bloqueado</option>
                </select>
            </div>
        </div>

        <div class="table-card user-list-card">
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th> <th>Registro</th>
                    <th>País/Ciudad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody id="user-table-body">
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $user):
                        $id = htmlspecialchars($user['id']);
                        $nombre_usuario = htmlspecialchars($user['nombre_usuario'] ?? $user['nombre']);
                        $email = htmlspecialchars($user['email']);
                        $rol = htmlspecialchars($user['rol']);
                        $fecha_registro = date('d/m/Y', strtotime($user['fecha_registro']));
                        $pais_ciudad = htmlspecialchars($user['pais'] ?? 'N/D') . ' / ' . htmlspecialchars($user['ciudad'] ?? 'N/D');
                        $estado = htmlspecialchars($user['estado_registro']);

                        $estado_class = $estado === 'Bloqueado' ? 'status-blocked' : 'status-active';
                        $rol_class = $rol === 'Editor' ? 'role-editor' : 'role-jugador';
                        ?>
                        <tr data-id="<?= $id ?>" data-user="<?= $nombre_usuario ?>" data-email="<?= $email ?>" data-role="<?= $rol ?>" data-status="<?= $estado ?>">
                            <td>
                                <?php
                                if (!empty($user['foto_perfil'])) {
                                    echo "<img src='{$user['foto_perfil']}' class='avatar-small'>";
                                } else {
                                    $initial = strtoupper(substr($user['nombre_usuario'] ?? $user['nombre'], 0, 1));
                                    echo "<div class='avatar-small placeholder'>{$initial}</div>";
                                }
                                ?>
                            </td>
                            <td><?= $id ?></td>
                            <td><?= $nombre_usuario ?></td>
                            <td><?= $email ?></td>
                            <td><span class="user-role <?= $rol_class ?>"><?= $rol ?></span></td> <td><?= $fecha_registro ?></td>
                            <td><?= $pais_ciudad ?></td>
                            <td><span class="user-status <?= $estado_class ?>"><?= $estado ?></span></td>
                            <td>
                                <div class="action-buttons-group">
                                    <?php if ($estado === 'Activo'): ?>
                                        <form method="POST" action="/trabajoPreguntasYRespuestas/admin/accionUsuario" onsubmit="return confirm('¿Está seguro de bloquear a <?= $nombre_usuario ?>?');">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <input type="hidden" name="accion" value="bloquear">
                                            <button type="submit" class="btn-action btn-block" title="Bloquear"><i class='fas fa-lock'></i></button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/trabajoPreguntasYRespuestas/admin/accionUsuario" onsubmit="return confirm('¿Está seguro de desbloquear a <?= $nombre_usuario ?>?');">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <input type="hidden" name="accion" value="desbloquear">
                                            <button type="submit" class="btn-action btn-unblock" title="Desbloquear"><i class='fas fa-unlock-alt'></i></button>
                                        </form>
                                    <?php endif; ?>

                                    <?php
                                    $new_rol_name = ($rol === 'Jugador') ? 'Editor' : 'Jugador';
                                    $btn_class = ($rol === 'Jugador') ? 'btn-promote' : 'btn-demote';
                                    $btn_icon = ($rol === 'Jugador') ? 'fa-user-tag' : 'fa-user-alt';
                                    $confirm_msg = "¿Está seguro de cambiar el rol de {$nombre_usuario} a {$new_rol_name}?";
                                    ?>
                                    <form method="POST" action="/trabajoPreguntasYRespuestas/admin/accionUsuario" onsubmit="return confirm('<?= $confirm_msg ?>');">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="accion" value="cambiar_rol">
                                        <input type="hidden" name="current_rol" value="<?= $rol ?>">
                                        <button type="submit" class="btn-action <?= $btn_class ?>" title="Cambiar a <?= $new_rol_name ?>">
                                            <i class='fas <?= $btn_icon ?>'></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="/trabajoPreguntasYRespuestas/admin/accionUsuario" onsubmit="return confirm('ADVERTENCIA: ¿Está seguro de ELIMINAR permanentemente a <?= $nombre_usuario ?>? Esta acción es irreversible.');">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <button type="submit" class="btn-action btn-delete" title="Eliminar"><i class='fas fa-trash'></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No se encontraron usuarios.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="/trabajoPreguntasYRespuestas/public/js/adminUsuarios.js"></script>

<?php include(__DIR__ . "/../views/partials/footer.php"); ?>