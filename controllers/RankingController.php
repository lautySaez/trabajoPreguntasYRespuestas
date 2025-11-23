<?php
class RankingController {
    private $rankingModel;

    public function __construct($rankingModel)
    {
        $this->rankingModel = $rankingModel;
    }

    public function verRankings() {
        $top3 = $this->rankingModel->obtenerTopJugadores(5);
        include("views/rankings.php");
    }
}