<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\HomeController;
use Semeformation\Mvc\Cinema_crud\controllers\FavoriteController;
use Semeformation\Mvc\Cinema_crud\controllers\MovieController;
use Semeformation\Mvc\Cinema_crud\controllers\ShowtimesController;
use Semeformation\Mvc\Cinema_crud\controllers\CinemaController;
use Psr\Log\LoggerInterface;

/**
 * Description of Router
 *
 * @author User
 */
class Router
{
    private $homeCtrl;
    private $favoriteCtrl;
    private $cinemaCtrl;
    private $movieCtrl;
    private $showtimesCtrl;

    public function __construct(LoggerInterface $logger)
    {
        $this->homeCtrl = new HomeController($logger);
        $this->favoriteCtrl = new FavoriteController($logger);
        $this->movieCtrl = new MovieController($logger);
        $this->showtimesCtrl = new ShowtimesController($logger);
        $this->cinemaCtrl = new CinemaController($logger);
    }

    public function routeRequest()
    {
        try {
            // on "sainifie" les entrées
            $sanitizedEntries = filter_input_array(
                INPUT_GET,
                ['action' => FILTER_DEFAULT]
            );
            if ($sanitizedEntries && $sanitizedEntries['action'] !== '') {
                // si l'action demandée est la liste des cinémas
                if ($sanitizedEntries['action'] == "cinemasList") {
                    // Activation de la route cinemasList
                    $this->cinemaCtrl->cinemasList();
                } elseif ($sanitizedEntries['action'] == "deleteCinema") {
                    $this->cinemaCtrl->deleteCinema();
                } elseif ($sanitizedEntries['action'] == "editCinema") {
                    $this->cinemaCtrl->editCinema();
                } elseif ($sanitizedEntries['action'] == "moviesList") {
                    // Activation de la route moviesList
                    $this->movieCtrl->moviesList();
                } elseif ($sanitizedEntries['action'] == "deleteMovie") {
                    $this->movieCtrl->deleteMovie();
                } elseif ($sanitizedEntries['action'] == "editMovie") {
                    $this->movieCtrl->editMovie();
                } elseif ($sanitizedEntries['action'] == "movieShowtimes") {
                    // Activation de la route movieShowtimes
                    $this->showtimesCtrl->movieShowtimes();
                } elseif ($sanitizedEntries['action'] == "cinemaShowtimes") {
                    // Activation de la route cinemaShowtimes
                    $this->showtimesCtrl->cinemaShowtimes();
                } elseif ($sanitizedEntries['action'] == "editFavoriteMoviesList") {
                    // Activation de la route editFavoriteMoviesList
                    $this->favoriteCtrl->editFavoriteMoviesList();
                } elseif ($sanitizedEntries['action'] == "editFavoriteMovie") {
                    // Activation de la route editFavoriteMovie
                    $this->favoriteCtrl->editFavoriteMovie();
                } elseif ($sanitizedEntries['action'] == "deleteFavoriteMovie") {
                    // Activation de la route deleteFavoriteMovie
                    $this->favoriteCtrl->deleteFavoriteMovie();
                } elseif ($sanitizedEntries['action'] == "createNewUser") {
                    // Activation de la route createNewUser
                    $this->homeCtrl->createNewUser();
                } elseif ($sanitizedEntries['action'] == "logout") {
                    // Activation de la route logout
                    $this->homeCtrl->logout();
                } else {
                    // Activation de la route par défaut (page d'accueil)
                    $this->homeCtrl->home();
                }
            } else {
                // Activation de la route par défaut (page d'accueil)
                $this->homeCtrl->home();
            }
        } catch (Exception $e) {
            $this->homeCtrl->error($e);
        }
    }

}
