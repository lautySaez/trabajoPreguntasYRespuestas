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
    <h1>Juego de Preguntas y Respuestas</h1>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>
    <?php if (isset($_SESSION["usuario"])): ?>
        <nav>
            <a href="index.php?controller=LoginController&method=home">Inicio</a>
            <a href="index.php?controller=LoginController&method=logout">Cerrar sesión</a>
        </nav>
    <?php endif; ?>
</header>
<hr>
<main>


