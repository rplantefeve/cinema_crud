<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\models\Film;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController {

    private $film;

    public function __construct(LoggerInterface $logger) {
        $this->film = new Film($logger);
    }

    /**
     * Route Liste des films
     */
    function moviesList() {
        // on récupère la liste des films ainsi que leurs informations
        $films = $this->film->getMoviesList();

        // On génère la vue films
        $vue = new View("MoviesList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(['films' => $films]);
    }

}
