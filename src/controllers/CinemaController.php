<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\dao\CinemaDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of CinemaController
 *
 * @author User
 */
class CinemaController {

    private $cinemaDAO;

    public function __construct(LoggerInterface $logger) {
        $this->cinemaDAO = new CinemaDAO($logger);
    }

    /**
     * Route Liste des cinémas
     */
    public function cinemasList() {
        $isUserAdmin = false;

        session_start();
        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on récupère la liste des cinémas ainsi que leurs informations
        $cinemas = $this->cinemaDAO->getCinemasList();

        // On génère la vue films
        $vue = new View("CinemasList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinemas'     => $cinemas,
            'isUserAdmin' => $isUserAdmin]);
    }

}
