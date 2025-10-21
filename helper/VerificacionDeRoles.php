<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarRol($rolesPermitidos) {
    if (!isset($_SESSION["usuario"])) {
        header("Location: ../views/inicioSesion.php");
        exit;
    }

    if (!is_array($rolesPermitidos)) {
        $rolesPermitidos = [$rolesPermitidos];
    }

    if (!in_array($_SESSION["usuario"]["rol"], $rolesPermitidos)) {
        header("Location: ../views/noAutorizado.php");
        exit;
    }
}
