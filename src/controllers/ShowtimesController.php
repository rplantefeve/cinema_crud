<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\CinemaDAO;
use Semeformation\Mvc\Cinema_crud\dao\SeanceDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;
use DateTime;
use Semeformation\Mvc\Cinema_crud\models\Seance;

/**
 * Description of ShowtimesController
 *
 * @author User
 */
class ShowtimesController extends Controller
{
    private $seanceDAO;

    public function __construct(LoggerInterface $logger)
    {
        $this->seanceDAO = new SeanceDAO($logger);
        $this->seanceDAO->setCinemaDAO(new CinemaDAO($logger));
        $this->seanceDAO->setFilmDAO(new FilmDAO($logger));
    }

    /**
     * Route liste des séances d'un film
     *
     * @return void
     */
    public function movieShowtimes()
    {
        $isUserAdmin = $this->checkAdminRights();
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(
            INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT]
        );
        // si l'identifiant du film a bien été passé en GET'
        if (isset($sanitizedEntries) === true && $sanitizedEntries['filmID'] !== null && $sanitizedEntries['filmID'] !== "") {
            // on récupère l'identifiant du cinéma
            $filmID = $sanitizedEntries['filmID'];
            // puis on récupère les informations du film en question
            $film = $this->seanceDAO->getFilmDAO()->getMovieByID($filmID);
        } else {
            // sinon, on retourne à l'accueil
            header('Location: index.php');
            exit();
        }

        // on récupère la liste des cinémas de ce film
        $cinemas = $this->seanceDAO->getCinemaDAO()->getMovieCinemasByMovieID($filmID);
        $cinemasUnplanned = $this->seanceDAO->getCinemaDAO()->getNonPlannedCinemas($filmID);
        $seances = $this->seanceDAO->getAllCinemasShowtimesByMovieID(
            $cinemas,
            $filmID
        );

        // On génère la vue séances du film
        $vue = new View("MovieShowtimes");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(
            [
                'cinemas'          => $cinemas,
                'cinemasUnplanned' => $cinemasUnplanned,
                'film'             => $film,
                'seances'          => $seances,
                'adminConnected'   => $isUserAdmin,
            ]
        );
    }

    /**
     * Route liste des séances d'un cinéma
     *
     * @return void
     */
    public function cinemaShowtimes()
    {
        $isUserAdmin = $this->checkAdminRights();
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(
            INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]
        );

        // si l'identifiant du cinéma a bien été passé en GET
        if ($sanitizedEntries !== null && $sanitizedEntries['cinemaID'] !== null && $sanitizedEntries['cinemaID'] !== "") {
            // on récupère l'identifiant du cinéma
            $cinemaID = $sanitizedEntries['cinemaID'];
            // puis on récupère les informations du cinéma en question
            $cinema = $this->seanceDAO->getCinemaDAO()->getCinemaByID($cinemaID);
        } else {
            // sinon, on retourne à l'accueil
            header('Location: index.php');
            exit();
        }

        // on récupère la liste des films de ce cinéma
        $films = $this->seanceDAO->getFilmDAO()->getCinemaMoviesByCinemaID($cinemaID);
        $filmsUnplanned = $this->seanceDAO->getFilmDAO()->getNonPlannedMovies($cinemaID);
        // on récupère toutes les séances de films pour un cinéma donné
        $seances = $this->seanceDAO->getAllMoviesShowtimesByCinemaID(
            $films,
            $cinemaID
        );

        // On génère la vue séances du cinéma
        $vue = new View("CinemaShowtimes");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(
            [
                'cinema'         => $cinema,
                'films'          => $films,
                'filmsUnplanned' => $filmsUnplanned,
                'seances'        => $seances,
                'adminConnected' => $isUserAdmin,
            ]
        );
    }

    /**
     * Route ajout/modification d'une séance
     *
     * @return void
     */
    public function editShowtime()
    {
        $this->redirectIfNotNotConnectedOrNotAdmin();

        // init. des flags. Etat par défaut => je viens du cinéma et je créé
        $fromCinema = true;
        $fromFilm = false;
        $isItACreation = true;

        // init. des variables du formulaire
        $seanceOld = [
            'dateheureDebutOld' => '',
            'dateheureFinOld'   => '',
            'heureFinOld'       => '',
        ];
        $seance = null;

        // si l'on est en GET
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'GET') {
            // on assainie les variables
            $sanitizedEntries = filter_input_array(
                INPUT_GET,
                [
                    'cinemaID'   => FILTER_SANITIZE_NUMBER_INT,
                    'filmID'     => FILTER_SANITIZE_NUMBER_INT,
                    'from'       => FILTER_DEFAULT,
                    'heureDebut' => FILTER_DEFAULT,
                    'heureFin'   => FILTER_DEFAULT,
                    'version'    => FILTER_DEFAULT,
                ]
            );
            // pour l'instant, on vérifie les données en GET
            if ($sanitizedEntries !== null && isset($sanitizedEntries['cinemaID'], $sanitizedEntries['filmID'], $sanitizedEntries['from']) === true) {
                // on récupère l'identifiant du cinéma
                $cinemaID = $sanitizedEntries['cinemaID'];
                // l'identifiant du film
                $filmID = $sanitizedEntries['filmID'];
                // d'où vient on ?
                $from = $sanitizedEntries['from'];
                // puis on récupère les informations du cinéma en question
                $cinema = $this->seanceDAO->getCinemaDAO()->getCinemaByID($cinemaID);
                // puis on récupère les informations du film en question
                $film = $this->seanceDAO->getFilmDAO()->getMovieByID($filmID);

                // s'il on vient des séances du film
                if (strstr($sanitizedEntries['from'], 'movie') !== false) {
                    $fromCinema = false;
                    // on vient du film
                    $fromFilm = true;
                }

                // ici, on veut savoir si on modifie ou si on ajoute
                if (isset($sanitizedEntries['heureDebut'], $sanitizedEntries['heureFin'], $sanitizedEntries['version']) === true) {
                    // nous sommes dans le cas d'une modification
                    $isItACreation = false;
                    $seance = new Seance();
                    // on récupère les anciennes valeurs (utile pour retrouver la séance avant de la modifier
                    $seanceOld['dateheureDebutOld'] = $sanitizedEntries['heureDebut'];
                    $seanceOld['dateheureFinOld'] = $sanitizedEntries['heureFin'];
                    // dates PHP
                    $dateheureDebut = new DateTime($sanitizedEntries['heureDebut']);
                    $dateheureFin = new DateTime($sanitizedEntries['heureFin']);
                    // on récupère l'heure de début et de fin
                    $seance->setHeureDebut($dateheureDebut);
                    $seance->setHeureFin($dateheureFin);
                    // on récupère la version
                    $seance->setVersion($sanitizedEntries['version']);
                }
            } else {
                // sinon, on retourne à l'accueil
                header('Location: index.php');
                exit();
            }
            // sinon, on est en POST
        } elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
            // on assainie les variables
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                [
                    'cinemaID'               => FILTER_SANITIZE_NUMBER_INT,
                    'filmID'                 => FILTER_SANITIZE_NUMBER_INT,
                    'datedebut'              => FILTER_DEFAULT,
                    'heuredebut'             => FILTER_DEFAULT,
                    'datefin'                => FILTER_DEFAULT,
                    'heurefin'               => FILTER_DEFAULT,
                    'dateheurefinOld'        => FILTER_DEFAULT,
                    'dateheuredebutOld'      => FILTER_DEFAULT,
                    'version'                => FILTER_DEFAULT,
                    'from'                   => FILTER_DEFAULT,
                    'modificationInProgress' => FILTER_DEFAULT,
                ]
            );
            // si toutes les valeurs sont renseignées
            if ($sanitizedEntries !== null
                && isset(
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
                ) === true
            ) {
                // nous sommes en Français
                setlocale(LC_TIME, 'fra_fra');
                // date du jour de projection de la séance
                $datetimeDebut = new DateTime($sanitizedEntries['datedebut'] . ' ' . $sanitizedEntries['heuredebut']);
                $datetimeFin = new DateTime($sanitizedEntries['datefin'] . ' ' . $sanitizedEntries['heurefin']);
                // Est-on dans le cas d'une insertion ?
                if ($sanitizedEntries['modificationInProgress'] === null) {
                    // j'insère dans la base
                    $this->seanceDAO->insertNewShowtime(
                        $sanitizedEntries['cinemaID'],
                        $sanitizedEntries['filmID'],
                        $datetimeDebut->format("Y-m-d H:i"),
                        $datetimeFin->format("Y-m-d H:i"),
                        $sanitizedEntries['version']
                    );
                } else {
                    // c'est une mise à jour
                    $this->seanceDAO->updateShowtime(
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
                if (strstr($sanitizedEntries['from'], 'movie') !== false) {
                    header('Location: index.php?action=movieShowtimes&filmID=' . $sanitizedEntries['filmID']);
                    exit;
                } else {
                    header('Location: index.php?action=cinemaShowtimes&cinemaID=' . $sanitizedEntries['cinemaID']);
                    exit;
                }
            }
        } else {
            // sinon, on retourne à l'accueil
            header('Location: index.php');
            exit();
        }

        // On génère la vue édition d'une séance
        $vue = new View("EditShowtime");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(
            [
                'cinema'        => $cinema,
                'film'          => $film,
                'seance'        => $seance,
                'seanceOld'     => $seanceOld,
                'from'          => $from,
                'isItACreation' => $isItACreation,
                'fromCinema'    => $fromCinema,
                'fromFilm'      => $fromFilm,
            ]
        );
    }

    /**
     * Route suppression d'une séance
     *
     * @return never
     */
    public function deleteShowtime()
    {
        $this->redirectIfNotNotConnectedOrNotAdmin();

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on assainie les variables
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                [
                    'cinemaID'   => FILTER_SANITIZE_NUMBER_INT,
                    'filmID'     => FILTER_SANITIZE_NUMBER_INT,
                    'heureDebut' => FILTER_DEFAULT,
                    'heureFin'   => FILTER_DEFAULT,
                    'version'    => FILTER_DEFAULT,
                    'from'       => FILTER_DEFAULT,
                ]
            );

            // suppression de la séance
            $this->seanceDAO->deleteShowtime(
                $sanitizedEntries['cinemaID'],
                $sanitizedEntries['filmID'],
                $sanitizedEntries['heureDebut'],
                $sanitizedEntries['heureFin']
            );
            // en fonction d'où je viens, je redirige
            if (strstr($sanitizedEntries['from'], 'movie') !== false) {
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
