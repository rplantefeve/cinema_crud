<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of CinemaController
 *
 * @author User
 */
class CinemaController {

    private $cinema;

    public function __construct(LoggerInterface $logger) {
        $this->cinema = new Cinema($logger);
    }

    /**
     * Route Liste des cinémas
     */
    public function cinemasList() {
        // on récupère la liste des cinémas ainsi que leurs informations
        $cinemas = $this->cinema->getCinemasList();

        // On génère la vue films
        $vue = new View("CinemasList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(['cinemas' => $cinemas]);
    }

}
