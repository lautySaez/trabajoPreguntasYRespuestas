<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preguntas y Respuestas</title>
    <link rel="stylesheet" href="public/css/estilos.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
<header>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $usuario = $_SESSION["usuario"] ?? null;
    ?>
    
    <div class="header-container">
        <!-- Logo a la izquierda -->
        <div class="logo-section">
            <h1>AciertaYa - Juego de Preguntas</h1>
        </div>
        
        <!-- Usuario a la derecha -->
        <?php if ($usuario): ?>
        <div class="user-section">
            <div class="user-info" onclick="toggleUserMenu()">
                <?php if (!empty($usuario["foto_perfil"])): ?>
                    <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" alt="Foto de perfil" class="user-avatar">
                <?php else: ?>
                    <div class="user-avatar-placeholder">
                        <i class="user-icon">👤</i>
                    </div>
                <?php endif; ?>
                <div class="user-details">
                    <span class="user-name"><?= htmlspecialchars($usuario["nombre"]) ?></span>
                    <span class="user-email"><?= htmlspecialchars($usuario["email"]) ?></span>
                </div>
                <span class="dropdown-arrow">▼</span>
            </div>
            
            <div class="user-dropdown" id="userDropdown">
                <a href="index.php?controller=LoginController&method=home" class="dropdown-item">
                    <i class="icon">🏠</i>
                    <span>Inicio</span>
                </a>
                <a href="index.php?controller=LoginController&method=logout" class="dropdown-item logout">
                    <i class="icon">🚪</i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Cerrar el menú si se hace clic fuera de él
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.user-section')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        });
    </script>
</header>
<hr>
<main>


