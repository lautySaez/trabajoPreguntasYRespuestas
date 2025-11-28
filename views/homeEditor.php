<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="/public/css/homeEditor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
$usuario = $_SESSION["usuario"] ?? null;
require_once("helper/VerificacionDeRoles.php");
verificarRol("Editor");
?>

    <div class="editor-dashboard-container">
        <?php if ($usuario && $usuario["rol"] === "Editor"): ?>

            <aside class="sidebar-menu">
                <div class="perfil-section">
                    <h1>Panel Editor</h1>
                    <h2>Hola, <?= htmlspecialchars($usuario["nombre"]) ?></h2>

                    <?php if (!empty($usuario["foto_perfil"])): ?>
                        <img src="<?= htmlspecialchars($usuario["foto_perfil"]) ?>" alt="Perfil" class="foto-perfil">
                    <?php else: ?>
                        <div class="foto-perfil-placeholder">
                            <i class="fa-solid fa-user-astronaut"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <nav class="dashboard-nav">
                    <a href="/editor/gestionarPreguntas" class="nav-item">
                        <span class="icon"><i class="fa-solid fa-list-check"></i></span> Gestionar Preguntas
                    </a>

                    <a href="/editor/crearPregunta" class="nav-item create-btn">
                        <span class="icon"><i class="fa-solid fa-plus"></i></span> Crear Pregunta
                    </a>

                    <a href="/editor/gestionarCategorias" class="nav-item">
                        <span class="icon"><i class="fa-solid fa-tags"></i></span> Gestionar Categorías
                    </a>

                    <a href="/editor/crearCategoria" class="nav-item create-btn">
                        <span class="icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span> Crear Categoría
                    </a>

                    <a href="/editor/preguntasReportadas" class="nav-item">
                        <span class="icon"><i class="fa-solid fa-triangle-exclamation"></i></span> Reportes
                    </a>
                    <a href="/sugerencia/pendientes" class="nav-item create-btn">
                        <span class="icon"><i class="fa-solid fa-lightbulb"></i></span> Sugerencias Pendientes
                    </a>
                </nav>
            </aside>

            <main class="dashboard-content" id="dashboardContent">
                <div id="bubblesContainer" class="bubbles-container"></div>

                <div class="content-wrapper">
                    <div class="header-pro">
                        <h2>Centro de Control</h2>
                        <p>Selecciona una acción para comenzar a trabajar.</p>
                    </div>

                    <div class="cards-container">
                        <a href="/editor/gestionarPreguntas" class="card">
                            <i class="fa-solid fa-file-pen"></i>
                            <h3>Gestión de Preguntas</h3>
                            <p>Edita, elimina o revisa el banco de preguntas actual.</p>
                        </a>

                        <a href="/editor/crearPregunta" class="card highlight-card">
                            <i class="fa-solid fa-circle-plus"></i>
                            <h3>Crear Nueva Pregunta</h3>
                            <p>Añade nuevo contenido y desafíos al juego.</p>
                        </a>

                        <a href="/editor/gestionarCategorias" class="card">
                            <i class="fa-solid fa-layer-group"></i>
                            <h3>Gestión de Categorías</h3>
                            <p>Administra los temas y colores de las categorías.</p>
                        </a>

                        <a href="/editor/crearCategoria" class="card highlight-card">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <h3>Nueva Categoría</h3>
                            <p>Define una nueva área de conocimiento.</p>
                        </a>
                        <a href="/sugerencia/pendientes" class="card">
                            <i class="fa-solid fa-lightbulb"></i>
                            <h3>Revisar Sugerencias</h3>
                            <p>Aprueba o rechaza preguntas aportadas por jugadores.</p>
                        </a>
                    </div>
                </div>
            </main>

        <?php else: ?>
            <div class="error-container">
                <p class="error-msg">Error: Acceso denegado o sesión no válida.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="/public/js/homeEditor.js"></script>

<?php include("views/partials/footer.php"); ?>