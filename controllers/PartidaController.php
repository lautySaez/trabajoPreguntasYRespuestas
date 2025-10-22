<?php
class PartidaController
{
    public function __construct($usuarioModel = null) {
    // Constructor vacio para que no falle el router
    }

    public function mostrarReglas()
    {
        include("views/reglas.php");
    }

    /*public function iniciarPartida()
    {
        include("views/partida.php");
    }*/

    public function terminarPartida()
    {
        header("Location: index.php?controller=LoginController&method=home");
        exit();
    }

    public function iniciarPartida()
    { 
    // pregunta ej
    $preguntaActual = [
        "texto" => "¿Cuál es la mejor ciudad del mundo?",
        "respuestas" => [
            ["id" => 1, "texto" => "Berlin"],
            ["id" => 2, "texto" => "Madrid"],
            ["id" => 3, "texto" => "Paris"],
            ["id" => 4, "texto" => "Buenos Aires"]
        ]
    ];

    include("views/partida.php");
}

}
