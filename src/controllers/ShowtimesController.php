<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\exceptions\BusinessObjectAlreadyExists;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;
use DateTime;
use Semeformation\Mvc\Cinema_crud\models\Seance;

/**
 * Description of ShowtimesController
 *
 * @author User
 */
class ShowtimesController extends Controller
{
    /**
     * Route liste des séances d'un film
     * @param string $filmId
     * @param Request $request
     * @param Application $app
     * @return string
     */
    public function movieShowtimes(
        string $filmId = null,
        Request $request = null,
        Application $app = null
    ) {
        $adminConnected = $this->checkIfUserIsConnectedAndAdmin($app);

        // si l'identifiant du film a bien été passé en GET
        if ($filmId !== null && $filmId !== "") {
            // puis on récupère les informations du film en question
            $film = $app['dao.seance']->getFilmDAO()->find($filmId);

            // on récupère les cinémas qui ne projettent pas encore le film
            $cinemasUnplanned = $app['dao.seance']->getCinemaDAO()->findAllByFilmIdNotIn($filmId);
        } else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // on récupère la liste des cinémas de ce film
        $cinemas = $app['dao.seance']->getCinemaDAO()->findAllByFilmId($filmId);
        $seances = $app['dao.seance']->findAllByFilmId($cinemas, $filmId);

        // données de la vue
        $donnees = [
            'titre'            => 'Séances du film ',
            'cinemas'          => $cinemas,
            'film'             => $film,
            'seances'          => $seances,
            'cinemasUnplanned' => $cinemasUnplanned,
            'adminConnected'   => $adminConnected];
        // On génère la vue séances du film
        return $app['twig']->render('showtimes.movie.html.twig', $donnees);
    }

    /**
     * Route liste des séances d'un cinéma
     * @param Request $request
     * @param Application $app
     * @param string $cinemaId
     * @return string
     */
    public function cinemaShowtimes(
        Request $request = null,
        Application $app = null,
        string $cinemaId = null
    ) {
        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        $adminConnected = $this->checkIfUserIsConnectedAndAdmin($app);

        // si l'identifiant du cinéma a bien été passé en GET
        if ($cinemaId !== null && $cinemaId !== "") {
            // puis on récupère les informations du cinéma en question
            $cinema = $app['dao.seance']->getCinemaDAO()->find($cinemaId);

            // on récupère les films pas encore projetés
            $filmsUnplanned = $app['dao.seance']->getFilmDAO()->findAllByCinemaIdNotIn($cinemaId);
        } else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // on récupère la liste des films de ce cinéma
        $films = $app['dao.seance']->getFilmDAO()->findAllByCinemaId($cinemaId);
        // on récupère toutes les séances de films pour un cinéma donné
        $seances = $app['dao.seance']->findAllByCinemaId($films, $cinemaId);

        // données de la vue
        $donnees = [
            'titre'          => 'Séances du cinéma ',
            'cinema'         => $cinema,
            'films'          => $films,
            'seances'        => $seances,
            'filmsUnplanned' => $filmsUnplanned,
            'adminConnected' => $adminConnected];
        // On génère la vue séances du cinéma
        return $app['twig']->render('showtimes.cinema.html.twig', $donnees);
    }

    /**
     * Route pour supprimer une séance
     * @param Request $request
     * @param Application $app
     * @param string $filmId
     * @param string $cinemaId
     * @return RedirectResponse
     */
    public function deleteShowtime(
        Request $request = null,
        Application $app = null,
        string $filmId = null,
        string $cinemaId = null
    ) {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request, $app);

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on assainie les variables
            $entries = $this->extractArrayFromPostRequest(
                $request,
                [
                    'heureDebut',
                    'heureFin',
                    'version',
                    'from',
                ]
            );
            $entries['cinemaID'] = $cinemaId;
            $entries['filmID'] = $filmId;

            // suppression de la séance
            $app['dao.seance']->delete(
                $entries['cinemaID'],
                $entries['filmID'],
                $entries['heureDebut'],
                $entries['heureFin'],
            );
            // en fonction d'où je viens, je redirige
            if (strstr($entries['from'], 'movie') !== false) {
                return $app->redirect($request->getBasePath() . '/showtime/movie/' . $filmId);
            } else {
                return $app->redirect($request->getBasePath() . '/showtime/cinema/' . $cinemaId);
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
    public function editShowtime(
        Request $request = null,
        Application $app = null,
        string $filmId = null,
        string $cinemaId = null
    ) {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        $this->redirectIfUserNotConnectedOrNotAdmin($request, $app);

        // init. des flags. Etat par défaut => je viens du cinéma et je créé
        $fromCinema = true;
        $fromFilm = false;
        $isItACreation = true;
        $alreadyExists = false;

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
            $entries = $this->extractArrayFromGetRequest(
                $request,
                [
                    'from',
                    'heureDebut',
                    'heureFin',
                    'version',
                    'filmID',
                    'cinemaID',
                ]
            );
            // si le filmID n'était pas dans la requête GET
            if ($entries['filmID'] === null && $filmId !== null) {
                // il est dans la route
                $entries['filmID'] = $filmId;
            }
            // si le cinemaID n'était pas dans la requête GET
            if ($entries['cinemaID'] === null && $cinemaId !== null) {
                // il est dans la route
                $entries['cinemaID'] = $cinemaId;
            }
            // pour l'instant, on vérifie les données en GET
            if (
                isset(
                    $entries['cinemaID'],
                    $entries['filmID'],
                    $entries['from']
                ) === true
            ) {
                // on récupère l'identifiant du cinéma
                $cinemaID = $entries['cinemaID'];
                // l'identifiant du film
                $filmID = $entries['filmID'];
                // d'où vient on ?
                $from = $entries['from'];

                // puis on récupère les informations du cinéma en question
                $cinema = $app['dao.seance']->getCinemaDAO()->find($cinemaID);

                // puis on récupère les informations du film en question
                $film = $app['dao.seance']->getFilmDAO()->find($filmID);

                // s'il on vient des séances du film
                if (strstr($from, 'movie') !== false) {
                    $fromCinema = false;
                    // on vient du film
                    $fromFilm = true;
                }

                // ici, on veut savoir si on modifie ou si on ajoute
                if (
                    isset(
                        $entries['heureDebut'],
                        $entries['heureFin'],
                        $entries['version']
                    ) === true
                ) {
                    // nous sommes dans le cas d'une modification
                    $isItACreation = false;
                    $seance = new Seance();
                    // on récupère les anciennes valeurs (utile pour retrouver la séance avant de la modifier
                    $seanceOld['dateheureDebutOld'] = $entries['heureDebut'];
                    $seanceOld['dateheureFinOld'] = $entries['heureFin'];
                    // dates PHP
                    $dateheureDebut = new DateTime($entries['heureDebut']);
                    $dateheureFin = new DateTime($entries['heureFin']);
                    // découpage en heures
                    $seance->setHeureDebut($dateheureDebut);
                    $seance->setHeureFin($dateheureFin);
                    // on récupère la version
                    $seance->setVersion($entries['version']);
                }
            } else {
                // en fonction d'où je viens, je redirige
                return $this->redirectFrom($app, $request, $entries['from'], $entries['filmID'] ?? "", $entries['cinemaID'] ?? "");
            }
            // sinon, on est en POST
        } elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
            // on assainie les variables
            $entries = $this->extractArrayFromPostRequest(
                $request,
                [
                    'datedebut',
                    'heuredebut',
                    'datefin',
                    'heurefin',
                    'dateheurefinOld',
                    'dateheuredebutOld',
                    'version',
                    'from',
                    'modificationInProgress',
                ]
            );
            $entries['cinemaID'] = $cinemaId;
            $entries['filmID'] = $filmId;
            // d'où vient on ?
            $from = $entries['from'];
            // si toutes les valeurs sont renseignées
            if ($entries !== null
                && isset(
                    $entries['cinemaID'],
                    $entries['filmID'],
                    $entries['datedebut'],
                    $entries['heuredebut'],
                    $entries['datefin'],
                    $entries['heurefin'],
                    $entries['dateheuredebutOld'],
                    $entries['dateheurefinOld'],
                    $entries['version'],
                    $entries['from']
                ) === true
            ) {
                // nous sommes en Français
                setlocale(LC_TIME, 'fra_fra');
                // date du jour de projection de la séance
                $datetimeDebut = DateTime::createFromFormat(
                    'Y-m-d H:i',
                    $entries['datedebut'] . ' ' . $entries['heuredebut']
                );
                $datetimeFin = DateTime::createFromFormat(
                    'Y-m-d H:i',
                    $entries['datefin'] . ' ' . $entries['heurefin']
                );
                if ($datetimeDebut === false) {
                    $datetimeDebut = new DateTime();
                }
                if ($datetimeFin === false) {
                    $datetimeFin = new DateTime();
                }
                // je récupère l'objet Cinema
                $cinema = $app['dao.seance']->getCinemaDAO()->find($entries['cinemaID']);
                // je récupère l'objet Film
                $film = $app['dao.seance']->getFilmDAO()->find($entries['filmID']);
                // on crée l'objet Seance
                $seance = new Seance();
                $seance->setHeureDebut($datetimeDebut);
                $seance->setHeureFin($datetimeFin);
                $seance->setVersion($entries['version']);
                $seance->setCinema($cinema);
                $seance->setFilm($film);
                try {
                    // je sauvegarde l'objet en BDD
                    $app['dao.seance']->save(
                        $seance,
                        $entries['dateheuredebutOld'],
                        $entries['dateheurefinOld']
                    );
                    // en fonction d'où je viens, je redirige
                    return $this->redirectFrom($app, $request, $entries['from'], $entries['filmID'], $entries['cinemaID']);
                } catch (BusinessObjectAlreadyExists $ex) {
                    $alreadyExists = true;
                }
            }
        } else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        $donnees = [
            'titre'         => 'Séance du cinéma ',
            'cinema'        => $cinema,
            'film'          => $film,
            'seance'        => $seance,
            'seanceOld'     => $seanceOld,
            'from'          => $from,
            'isItACreation' => $isItACreation,
            'fromCinema'    => $fromCinema,
            'fromFilm'      => $fromFilm,
            'alreadyExists' => $alreadyExists];

        // On génère la vue édition d'une séance
        return $app['twig']->render('showtime.edit.html.twig', $donnees);
    }

    /**
     * Redirige en fonction d'où l'utilisateur vient
     *
     * @param Application $app
     * @param Request $request
     * @param string $from
     * @param string $filmId
     * @param string $cinemaId
     * @return RedirectResponse
     */
    private function redirectFrom(Application $app, Request $request, string $from, string $filmId = "", string $cinemaId = ""): RedirectResponse
    {
        // en fonction d'où je viens, je redirige
        if (strstr($from, 'movie') !== false) {
            // on redirige vers les séances du film
            return $app->redirect($request->getBasePath() . '/showtime/movie/' . $filmId);
        } else {
            // on redirige vers les séances du cinéma
            return $app->redirect($request->getBasePath() . '/showtime/cinema/' . $cinemaId);
        }
    }
}
