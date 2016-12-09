<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController {

    private $filmDAO;

    public function __construct(LoggerInterface $logger) {
        $this->filmDAO = new FilmDAO($logger);
    }

    /**
     * Route Liste des films
     */
    function moviesList() {
        $isUserAdmin = false;

        session_start();
        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on récupère la liste des films ainsi que leurs informations
        $films = $this->filmDAO->getMoviesList();

        // On génère la vue films
        $vue = new View("MoviesList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'films'       => $films,
            'isUserAdmin' => $isUserAdmin]);
    }

}
