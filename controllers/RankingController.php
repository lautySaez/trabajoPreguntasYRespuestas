<?php
class RankingController {
    private $rankingModel;

    public function __construct($rankingModel)
    {
        $this->rankingModel = $rankingModel;
    }

    public function verRankings() {
        $tipo = $_GET["tipo"] ?? "goat";

        if ($tipo === "goat") {
            $top3 = $this->rankingModel->obtenerTopJugadores(10);
        } 
        else if ($tipo === "mejores") {
            $top3 = $this->rankingModel->obtenerMejoresPartidas(10);
        }
        else {
            $top3 = []; 
        }

        include("views/rankings.php");
    }
    
}