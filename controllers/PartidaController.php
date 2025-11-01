<?php
class PartidaController
{
    public function __construct($usuarioModel = null) {}

    public function mostrarModo()
    {
        include("views/modoDeJuego.php");
    }

    public function guardarModo()
    {
        if (isset($_POST["modo"])) {
            $_SESSION["modo_juego"] = $_POST["modo"];
            header("Location: index.php?controller=partida&method=mostrarReglas");
            exit();
        } else {
            header("Location: index.php?controller=partida&method=mostrarModo");
            exit();
        }
    }

    public function mostrarReglas()
    {
        include("views/reglas.php");
    }

    public function mostrarRuleta()
    {
        include("views/ruleta.php");
    }

    public function iniciarPartida()
    {
        $preguntaActual = [
            "texto" => "¿Cuál es la mejor ciudad del mundo?",
            "respuestas" => [
                ["id" => 1, "texto" => "Berlín"],
                ["id" => 2, "texto" => "Madrid"],
                ["id" => 3, "texto" => "París"],
                ["id" => 4, "texto" => "Buenos Aires"]
            ]
        ];
        include("views/partida.php");
    }

    public function terminarPartida()
    {
        header("Location: index.php?controller=LoginController&method=home");
        exit();
    }

}