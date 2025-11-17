<?php
class RankingController {
    private $rankingModel;

    public function __construct($rankingModel)
    {
        $this->rankingModel = $rankingModel;
    }

    public function verRankings() {
        $rankings = $this->rankingModel->obtenerRanking();
        include("views/rankings.php");
    }
}