<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\views\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;
use Psr\Log\LoggerInterface;
use DateTime;

/**
 * Description of ShowtimesController
 *
 * @author User
 */
class ShowtimesController extends Controller {

    public function __construct(LoggerInterface $logger = null) {
        
    }

    /**
     * Route liste des séances d'un film
     * @param string $filmId
     * @param Request $request
     * @param Application $app
     * @return string
     */
    public function movieShowtimes(string $filmId, Request $request = null,
            Application $app = null) {
        $adminConnected = false;

        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if ($app['session']->get('user') and $app['session']->get('user')['username'] ==
                'admin@adm.adm') {
            $adminConnected = true;
        }

        // on assainit les entrées
        $entries['filmID'] = $filmId;
        // si l'identifiant du film a bien été passé en GET'
        if ($entries && !is_null($entries['filmID']) && $entries['filmID'] !== '') {
            // on récupère l'identifiant du cinéma
            $filmID = $entries['filmID'];
            // puis on récupère les informations du film en question
            $film   = $app['dao.seance']->getFilmDAO()->find($filmID);

            // on récupère les cinémas qui ne projettent pas encore le film
            $cinemasUnplanned = $app['dao.seance']->getCinemaDAO()->getNonPlannedCinemas($filmID);
        }
        // sinon, on retourne à l'accueil
        else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // on récupère la liste des cinémas de ce film
        $cinemas = $app['dao.seance']->getCinemaDAO()->getMovieCinemasByMovieID($filmID);
        $seances = $app['dao.seance']->getAllCinemasShowtimesByMovieID($cinemas,
                $filmID);

        // On génère la vue séances du film
        $vue = new View("MovieShowtimes");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
                        [
                    'cinemas'          => $cinemas,
                    'film'             => $film,
                    'seances'          => $seances,
                    'cinemasUnplanned' => $cinemasUnplanned,
                    'adminConnected'   => $adminConnected]);
    }

    /**
     * Route liste des séances d'un cinéma
     * @param Request $request
     * @param Application $app
     * @param string $cinemaId
     * @return string
     */
    public function cinemaShowtimes(Request $request = null,
            Application $app = null, string $cinemaId = null) {
        $adminConnected = false;

        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if ($app['session']->get('user') and $app['session']->get('user')['username'] ==
                'admin@adm.adm') {
            $adminConnected = true;
        }

        // on assainit les entrées
        $entries['cinemaID'] = $cinemaId;

        // si l'identifiant du cinéma a bien été passé en GET
        if ($entries && !is_null($entries) && $entries['cinemaID'] != '') {
            // on récupère l'identifiant du cinéma
            $cinemaID = $entries['cinemaID'];
            // puis on récupère les informations du cinéma en question
            $cinema   = $app['dao.seance']->getCinemaDAO()->find($cinemaID);

            // on récupère les films pas encore projetés
            $filmsUnplanned = $app['dao.seance']->getFilmDAO()->getNonPlannedMovies($cinemaID);
        }
        // sinon, on retourne à l'accueil
        else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // on récupère la liste des films de ce cinéma
        $films   = $app['dao.seance']->getFilmDAO()->getCinemaMoviesByCinemaID($cinemaID);
        // on récupère toutes les séances de films pour un cinéma donné
        $seances = $app['dao.seance']->getAllMoviesShowtimesByCinemaID($films,
                $cinemaID);

        // On génère la vue séances du cinéma
        $vue = new View("CinemaShowtimes");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
                        [
                    'cinema'         => $cinema,
                    'films'          => $films,
                    'seances'        => $seances,
                    'filmsUnplanned' => $filmsUnplanned,
                    'adminConnected' => $adminConnected]);
    }

    /**
     * Route pour supprimer une séance
     * @param Request $request
     * @param Application $app
     * @param string $filmId
     * @param string $cinemaId
     * @return RedirectResponse
     */
    public function deleteShowtime(Request $request = null,
            Application $app = null, string $filmId = null,
            string $cinemaId = null): RedirectResponse {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!$app['session']->get('user') or $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on assainie les variables
            $entries             = $this->extractArrayFromPostRequest($request,
                    ['heureDebut',
                'heureFin',
                'version',
                'from']);
            $entries['cinemaID'] = $cinemaId;
            $entries['filmID']   = $filmId;

            // suppression de la séance
            $app['dao.seance']->deleteShowtime($entries['cinemaID'],
                    $entries['filmID'], $entries['heureDebut'],
                    $entries['heureFin']
            );
            // en fonction d'où je viens, je redirige
            if (strstr($entries['from'], 'movie')) {
                return $app->redirect($request->getBasePath() . '/showtime/movie/' . $entries['filmID']);
            } else {
                return $app->redirect($request->getBasePath() . '/showtime/cinema/' . $entries['cinemaID']);
            }
        } else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }
    }

    /**
     * Route pour créer/modifier une séance
     * @param Request $request
     * @param Application $app
     * @param string $filmId
     * @param string $cinemaId
     * @return string
     */
    public function editShowtime(Request $request = null,
            Application $app = null, string $filmId = null,
            string $cinemaId = null) {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!$app['session']->get('user') or $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // init. des flags. Etat par défaut => je viens du cinéma et je créé
        $fromCinema    = true;
        $fromFilm      = false;
        $isItACreation = true;

        // init. des variables du formulaire
        $seanceOld = [
            'dateheureDebutOld' => '',
            'dateheureFinOld'   => '',
            'heureFinOld'       => ''];

        $seance = null;

        // si l'on est en GET
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET') {
            // on assainie les variables
            $entries = $this->extractArrayFromGetRequest($request,
                    ['from',
                'heureDebut',
                'heureFin',
                'version',
                'filmID',
                'cinemaID']);
            // si le filmID n'était pas dans la requête GET
            if (!$entries['filmID'] && $filmId) {
                // il est dans la route
                $entries['filmID'] = $filmId;
            }
            // si le cinemaID n'était pas dans la requête GET
            if (!$entries['cinemaID'] && $cinemaId) {
                // il est dans la route
                $entries['cinemaID'] = $cinemaId;
            }
            // pour l'instant, on vérifie les données en GET
            if ($entries && isset($entries['cinemaID'], $entries['filmID'],
                            $entries['from'])) {
                // on récupère l'identifiant du cinéma
                $cinemaID = $entries['cinemaID'];
                // l'identifiant du film
                $filmID   = $entries['filmID'];
                // d'où vient on ?
                $from     = $entries['from'];

                // puis on récupère les informations du cinéma en question
                $cinema = $app['dao.seance']->getCinemaDAO()->find($cinemaID);

                // puis on récupère les informations du film en question
                $film = $app['dao.seance']->getFilmDAO()->find($filmID);

                // s'il on vient des séances du film
                if (strstr($entries['from'], 'movie')) {
                    $fromCinema = false;
                    // on vient du film
                    $fromFilm   = true;
                }

                // ici, on veut savoir si on modifie ou si on ajoute
                if (isset($entries['heureDebut'], $entries['heureFin'],
                                $entries['version'])) {
                    // nous sommes dans le cas d'une modification
                    $isItACreation                  = false;
                    $seance                         = new \Semeformation\Mvc\Cinema_crud\models\Seance();
                    // on récupère les anciennes valeurs (utile pour retrouver la séance avant de la modifier
                    $seanceOld['dateheureDebutOld'] = $entries['heureDebut'];
                    $seanceOld['dateheureFinOld']   = $entries['heureFin'];
                    // dates PHP
                    $dateheureDebut                 = new DateTime($entries['heureDebut']);
                    $dateheureFin                   = new DateTime($entries['heureFin']);
                    // découpage en heures
                    $seance->setHeureDebut($dateheureDebut);
                    $seance->setHeureFin($dateheureFin);
                    // on récupère la version
                    $seance->setVersion($entries['version']);
                }
            }
            // sinon, on retourne à l'accueil
            else {
                // renvoi à la page d'accueil
                return $app->redirect($request->getBasePath() . '/home');
            }
            // sinon, on est en POST
        } else if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
            // on assainie les variables
            $entries             = $this->extractArrayFromPostRequest($request,
                    ['datedebut',
                'heuredebut',
                'datefin',
                'heurefin',
                'dateheurefinOld',
                'dateheuredebutOld',
                'version',
                'from',
                'modificationInProgress']);
            $entries['cinemaID'] = $cinemaId;
            $entries['filmID']   = $filmId;
            // si toutes les valeurs sont renseignées
            if ($entries && isset($entries['cinemaID'], $entries['filmID'],
                            $entries['datedebut'], $entries['heuredebut'],
                            $entries['datefin'], $entries['heurefin'],
                            $entries['dateheuredebutOld'],
                            $entries['dateheurefinOld'], $entries['version'],
                            $entries['from'])) {
                // nous sommes en Français
                setlocale(LC_TIME, 'fra_fra');
                // date du jour de projection de la séance
                $datetimeDebut = DateTime::createFromFormat('d/m/Y H:i',
                                $entries['datedebut'] . ' ' . $entries['heuredebut']);
                $datetimeFin   = DateTime::createFromFormat('d/m/Y H:i',
                                $entries['datefin'] . ' ' . $entries['heurefin']);
                // Est-on dans le cas d'une insertion ?
                if (!isset($entries['modificationInProgress'])) {
                    // j'insère dans la base
                    $resultat = $app['dao.seance']->insertNewShowtime($entries['cinemaID'],
                            $entries['filmID'],
                            $datetimeDebut->format("Y-m-d H:i"),
                            $datetimeFin->format("Y-m-d H:i"),
                            $entries['version']);
                } else {
                    // c'est une mise à jour
                    $resultat = $app['dao.seance']->updateShowtime($entries['cinemaID'],
                            $entries['filmID'], $entries['dateheuredebutOld'],
                            $entries['dateheurefinOld'],
                            $datetimeDebut->format("Y-m-d H:i"),
                            $datetimeFin->format("Y-m-d H:i"),
                            $entries['version']);
                }
                // en fonction d'où je viens, je redirige
                if (strstr($entries['from'], 'movie')) {
                    // on redirige vers les séances du film
                    return $app->redirect($request->getBasePath() . '/showtime/movie/' . $entries['filmID']);
                } else {
                    // on redirige vers les séances du cinéma
                    return $app->redirect($request->getBasePath() . '/showtime/cinema/' . $entries['cinemaID']);
                }
            }
        }
        // sinon, on retourne à l'accueil
        else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // On génère la vue édition d'une séance
        $vue = new View("EditShowtime");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
                        [
                    'cinema'        => $cinema,
                    'film'          => $film,
                    'seance'        => $seance,
                    'seanceOld'     => $seanceOld,
                    'from'          => $from,
                    'isItACreation' => $isItACreation,
                    'fromCinema'    => $fromCinema,
                    'fromFilm'      => $fromFilm
        ]);
    }

}
