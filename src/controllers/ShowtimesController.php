<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\CinemaDAO;
use Semeformation\Mvc\Cinema_crud\models\Seance;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of ShowtimesController
 *
 * @author User
 */
class ShowtimesController {

    private $cinemaDAO;
    private $filmDAO;
    private $seance;

    public function __construct(LoggerInterface $logger) {
        $this->cinemaDAO = new CinemaDAO($logger);
        $this->filmDAO = new FilmDAO($logger);
        $this->seance = new Seance($logger);
    }

    /**
     * Route liste des séances d'un film
     */
    public function movieShowtimes() {

        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(INPUT_GET,
                ['filmID' => FILTER_SANITIZE_NUMBER_INT]);
        // si l'identifiant du film a bien été passé en GET'
        if ($sanitizedEntries && !is_null($sanitizedEntries['filmID']) && $sanitizedEntries['filmID'] !== '') {
            // on récupère l'identifiant du cinéma
            $filmID = $sanitizedEntries['filmID'];
            // puis on récupère les informations du film en question
            $film = $this->filmDAO->getMovieByID($filmID);
        }
        // sinon, on retourne à l'accueil
        else {
            header('Location: index.php');
            exit();
        }

        // on récupère la liste des cinémas de ce film
        $cinemas = $this->cinemaDAO->getMovieCinemasByMovieID($filmID);
        $seances = $this->seance->getAllCinemasShowtimesByMovieID($cinemas,
                $filmID);

        // On génère la vue séances du film
        $vue = new View("MovieShowtimes");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinemas' => $cinemas,
            'film' => $film,
            'seances' => $seances]);
    }

    /**
     * Route liste des séances d'un cinéma
     */
    public function cinemaShowtimes() {
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(INPUT_GET,
                ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

        // si l'identifiant du cinéma a bien été passé en GET
        if ($sanitizedEntries && !is_null($sanitizedEntries) && $sanitizedEntries['cinemaID'] != '') {
            // on récupère l'identifiant du cinéma
            $cinemaID = $sanitizedEntries['cinemaID'];
            // puis on récupère les informations du cinéma en question
            $cinema = $this->cinemaDAO->getCinemaByID($cinemaID);
        }
        // sinon, on retourne à l'accueil
        else {
            header('Location: index.php');
            exit();
        }

        // on récupère la liste des films de ce cinéma
        $films = $this->filmDAO->getCinemaMoviesByCinemaID($cinemaID);
        // on récupère toutes les séances de films pour un cinéma donné
        $seances = $this->seance->getAllMoviesShowtimesByCinemaID($films,
                $cinemaID);

        // On génère la vue séances du cinéma
        $vue = new View("CinemaShowtimes");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinema' => $cinema,
            'films' => $films,
            'seances' => $seances]);
    }

}