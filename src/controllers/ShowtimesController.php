<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\models\Film;
use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Semeformation\Mvc\Cinema_crud\models\Seance;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;
use DateTime;

/**
 * Description of ShowtimesController
 *
 * @author User
 */
class ShowtimesController
{
    private $cinema;
    private $film;
    private $seance;

    public function __construct(LoggerInterface $logger)
    {
        $this->cinema = new Cinema($logger);
        $this->film = new Film($logger);
        $this->seance = new Seance($logger);
    }

    /**
     * Route liste des séances d'un film
     */
    public function movieShowtimes()
    {
        $isUserAdmin = false;

        session_start();
        // si l'utilisateur est connecté et qu'il est administrateur
        if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(
            INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT]
        );
        // si l'identifiant du film a bien été passé en GET'
        if ($sanitizedEntries && !is_null($sanitizedEntries['filmID']) && $sanitizedEntries['filmID'] !== '') {
            // on récupère l'identifiant du cinéma
            $filmID = $sanitizedEntries['filmID'];
            // puis on récupère les informations du film en question
            $film = $this->film->getMovieInformationsByID($filmID);
        }
        // sinon, on retourne à l'accueil
        else {
            header('Location: index.php');
            exit();
        }

        // on récupère la liste des cinémas de ce film
        $cinemas = $this->cinema->getMovieCinemasByMovieID($filmID);
        $cinemasUnplanned = $this->film->getNonPlannedCinemas($filmID);
        $seances = $this->seance->getAllCinemasShowtimesByMovieID(
            $cinemas,
            $filmID
        );

        // On génère la vue séances du film
        $vue = new View("MovieShowtimes");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinemas' => $cinemas,
            'cinemasUnplanned' => $cinemasUnplanned,
            'film' => $film,
            'seances' => $seances,
            'adminConnected' => $isUserAdmin,
        ]);
    }

    /**
     * Route liste des séances d'un cinéma
     */
    public function cinemaShowtimes()
    {
        $isUserAdmin = false;

        session_start();
        // si l'utilisateur est connecté et qu'il est administrateur
        if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(
            INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]
        );

        // si l'identifiant du cinéma a bien été passé en GET
        if ($sanitizedEntries && !is_null($sanitizedEntries) && $sanitizedEntries['cinemaID'] != '') {
            // on récupère l'identifiant du cinéma
            $cinemaID = $sanitizedEntries['cinemaID'];
            // puis on récupère les informations du cinéma en question
            $cinema = $this->cinema->getCinemaInformationsByID($cinemaID);
        }
        // sinon, on retourne à l'accueil
        else {
            header('Location: index.php');
            exit();
        }

        // on récupère la liste des films de ce cinéma
        $films = $this->film->getCinemaMoviesByCinemaID($cinemaID);
        $filmsUnplanned = $this->cinema->getNonPlannedMovies($cinemaID);
        // on récupère toutes les séances de films pour un cinéma donné
        $seances = $this->seance->getAllMoviesShowtimesByCinemaID(
            $films,
            $cinemaID
        );

        // On génère la vue séances du cinéma
        $vue = new View("CinemaShowtimes");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinema' => $cinema,
            'films' => $films,
            'filmsUnplanned' => $filmsUnplanned,
            'seances' => $seances,
            'adminConnected' => $isUserAdmin,
        ]);
    }

    /**
     * Route ajout/modification d'une séance
     *
     * @return void
     */
    public function editShowtime()
    {
        session_start();
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!array_key_exists("user", $_SESSION) or $_SESSION['user'] !== 'admin@adm.adm') {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }

        // init. des flags. Etat par défaut => je viens du cinéma et je créé
        $fromCinema = true;
        $fromFilm = false;
        $isItACreation = true;

        // init. des variables du formulaire
        $seance = [
            'dateDebut' => '',
            'heureDebut' => '',
            'dateFin' => '',
            'heureFin' => '',
            'dateheureDebutOld' => '',
            'dateheureFinOld' => '',
            'heureFinOld' => '',
            'version' => '',
        ];

        // si l'on est en GET
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET') {
            // on assainie les variables
            $sanitizedEntries = filter_input_array(
                INPUT_GET,
                [
                    'cinemaID' => FILTER_SANITIZE_NUMBER_INT,
                    'filmID' => FILTER_SANITIZE_NUMBER_INT,
                    'from' => FILTER_DEFAULT,
                    'heureDebut' => FILTER_DEFAULT,
                    'heureFin' => FILTER_DEFAULT,
                    'version' => FILTER_DEFAULT,
                ]
            );
            // pour l'instant, on vérifie les données en GET
            if ($sanitizedEntries && isset(
                $sanitizedEntries['cinemaID'],
                $sanitizedEntries['filmID'],
                $sanitizedEntries['from']
            )) {
                // on récupère l'identifiant du cinéma
                $cinemaID = $sanitizedEntries['cinemaID'];
                // l'identifiant du film
                $filmID = $sanitizedEntries['filmID'];
                // d'où vient on ?
                $from = $sanitizedEntries['from'];
                // puis on récupère les informations du cinéma en question
                $cinema = $this->cinema->getCinemaInformationsByID($cinemaID);
                // puis on récupère les informations du film en question
                $film = $this->film->getMovieInformationsByID($filmID);
                // on récupère les cinémas qui ne projettent pas encore le film
                $cinemasUnplanned = $this->film->getNonPlannedCinemas($filmID);

                // s'il on vient des séances du film
                if (strstr($sanitizedEntries['from'], 'movie')) {
                    $fromCinema = false;
                    // on vient du film
                    $fromFilm = true;
                }

                // ici, on veut savoir si on modifie ou si on ajoute
                if (isset(
                    $sanitizedEntries['heureDebut'],
                    $sanitizedEntries['heureFin'],
                    $sanitizedEntries['version']
                )) {
                    // nous sommes dans le cas d'une modification
                    $isItACreation = false;
                    // on récupère les anciennes valeurs (utile pour retrouver la séance avant de la modifier
                    $seance['dateheureDebutOld'] = $sanitizedEntries['heureDebut'];
                    $seance['dateheureFinOld'] = $sanitizedEntries['heureFin'];
                    // dates PHP
                    $dateheureDebut = new DateTime($sanitizedEntries['heureDebut']);
                    $dateheureFin = new DateTime($sanitizedEntries['heureFin']);
                    // découpage en heures
                    $seance['heureDebut'] = $dateheureDebut->format("H:i");
                    $seance['heureFin'] = $dateheureFin->format("H:i");
                    // découpage en jour/mois/année
                    $seance['dateDebut'] = $dateheureDebut->format("Y-m-d");
                    $seance['dateFin'] = $dateheureFin->format("Y-m-d");
                    // on récupère la version
                    $seance['version'] = $sanitizedEntries['version'];
                }
            }
            // sinon, on retourne à l'accueil
            else {
                header('Location: index.php');
                exit();
            }
            // sinon, on est en POST
        } elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
            // on assainie les variables
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                [
                    'cinemaID' => FILTER_SANITIZE_NUMBER_INT,
                    'filmID' => FILTER_SANITIZE_NUMBER_INT,
                    'datedebut' => FILTER_DEFAULT,
                    'heuredebut' => FILTER_DEFAULT,
                    'datefin' => FILTER_DEFAULT,
                    'heurefin' => FILTER_DEFAULT,
                    'dateheurefinOld' => FILTER_DEFAULT,
                    'dateheuredebutOld' => FILTER_DEFAULT,
                    'version' => FILTER_DEFAULT,
                    'from' => FILTER_DEFAULT,
                    'modificationInProgress' => FILTER_DEFAULT,
                ]
            );
            // si toutes les valeurs sont renseignées
            if ($sanitizedEntries && isset(
                $sanitizedEntries['cinemaID'],
                $sanitizedEntries['filmID'],
                $sanitizedEntries['datedebut'],
                $sanitizedEntries['heuredebut'],
                $sanitizedEntries['datefin'],
                $sanitizedEntries['heurefin'],
                $sanitizedEntries['dateheuredebutOld'],
                $sanitizedEntries['dateheurefinOld'],
                $sanitizedEntries['version'],
                $sanitizedEntries['from']
            )) {
                // nous sommes en Français
                setlocale(LC_TIME, 'fra_fra');
                // date du jour de projection de la séance
                $datetimeDebut = new DateTime($sanitizedEntries['datedebut'] . ' ' . $sanitizedEntries['heuredebut']);
                $datetimeFin = new DateTime($sanitizedEntries['datefin'] . ' ' . $sanitizedEntries['heurefin']);
                // Est-on dans le cas d'une insertion ?
                if (!isset($sanitizedEntries['modificationInProgress'])) {
                    // j'insère dans la base
                    $resultat = $this->seance->insertNewShowtime(
                        $sanitizedEntries['cinemaID'],
                        $sanitizedEntries['filmID'],
                        $datetimeDebut->format("Y-m-d H:i"),
                        $datetimeFin->format("Y-m-d H:i"),
                        $sanitizedEntries['version']
                    );
                } else {
                    // c'est une mise à jour
                    $resultat = $this->seance->updateShowtime(
                        $sanitizedEntries['cinemaID'],
                        $sanitizedEntries['filmID'],
                        $sanitizedEntries['dateheuredebutOld'],
                        $sanitizedEntries['dateheurefinOld'],
                        $datetimeDebut->format("Y-m-d H:i"),
                        $datetimeFin->format("Y-m-d H:i"),
                        $sanitizedEntries['version']
                    );
                }
                // en fonction d'où je viens, je redirige
                if (strstr($sanitizedEntries['from'], 'movie')) {
                    header('Location: index.php?action=movieShowtimes&filmID=' . $sanitizedEntries['filmID']);
                    exit;
                } else {
                    header('Location: index.php?action=cinemaShowtimes&cinemaID=' . $sanitizedEntries['cinemaID']);
                    exit;
                }
            }
        }
        // sinon, on retourne à l'accueil
        else {
            header('Location: index.php');
            exit();
        }

        // On génère la vue films
        $vue = new View("EditShowtime");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinema' => $cinema, 'film' => $film, 'seance' => $seance, 'from' => $from, 'isItACreation' => $isItACreation, 'fromCinema' => $fromCinema,
        ]);
    }

    /**
     * Route suppression d'une séance
     *
     * @return void
     */
    public function deleteShowtime()
    {
        session_start();
        // si l'utilisateur n'est pas connecté
        if (!array_key_exists("user", $_SESSION)) {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on assainie les variables
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                [
                    'cinemaID' => FILTER_SANITIZE_NUMBER_INT,
                    'filmID' => FILTER_SANITIZE_NUMBER_INT,
                    'heureDebut' => FILTER_DEFAULT,
                    'heureFin' => FILTER_DEFAULT,
                    'version' => FILTER_DEFAULT,
                    'from' => FILTER_DEFAULT,
                ]
            );

            // suppression de la séance
            $this->seance->deleteShowtime(
                $sanitizedEntries['cinemaID'],
                $sanitizedEntries['filmID'],
                $sanitizedEntries['heureDebut'],
                $sanitizedEntries['heureFin']
            );
            // en fonction d'où je viens, je redirige
            if (strstr($sanitizedEntries['from'], 'movie')) {
                header('Location: index.php?action=movieShowtimes&filmID=' . $sanitizedEntries['filmID']);
                exit;
            } else {
                header('Location: index.php?action=cinemaShowtimes&cinemaID=' . $sanitizedEntries['cinemaID']);
                exit;
            }
        } else {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }
    }
}
