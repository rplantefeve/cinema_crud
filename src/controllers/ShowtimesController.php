<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\exceptions\BusinessObjectAlreadyExists;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;
use DateTime;

/**
 * Description of ShowtimesController
 *
 * @author User
 */
class ShowtimesController extends Controller {

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
        if ($app['session']->get('user') && $app['session']->get('user')['username'] ==
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
            $cinemasUnplanned = $app['dao.seance']->getCinemaDAO()->findAllByFilmIdNotIn($filmID);
        }
        // sinon, on retourne à l'accueil
        else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // on récupère la liste des cinémas de ce film
        $cinemas = $app['dao.seance']->getCinemaDAO()->findAllByFilmId($filmID);
        $seances = $app['dao.seance']->findAllByFilmId($cinemas, $filmID);

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
    public function cinemaShowtimes(Request $request = null,
            Application $app = null, string $cinemaId = null) {
        $adminConnected = false;

        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if ($app['session']->get('user') && $app['session']->get('user')['username'] ==
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
            $filmsUnplanned = $app['dao.seance']->getFilmDAO()->findAllByCinemaIdNotIn($cinemaID);
        }
        // sinon, on retourne à l'accueil
        else {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // on récupère la liste des films de ce cinéma
        $films   = $app['dao.seance']->getFilmDAO()->findAllByCinemaId($cinemaID);
        // on récupère toutes les séances de films pour un cinéma donné
        $seances = $app['dao.seance']->findAllByCinemaId($films, $cinemaID);

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
    public function deleteShowtime(Request $request = null,
            Application $app = null, string $filmId = null,
            string $cinemaId = null): RedirectResponse {
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!$app['session']->get('user') || $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on assainie les variables
            $entries             = $this->extractArrayFromPostRequest($request,
                    [
                'heureDebut',
                'heureFin',
                'version',
                'from']);
            $entries['cinemaID'] = $cinemaId;
            $entries['filmID']   = $filmId;

            // suppression de la séance
            $app['dao.seance']->delete($entries['cinemaID'], $entries['filmID'],
                    $entries['heureDebut'], $entries['heureFin']
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
        if (!$app['session']->get('user') || $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // init. des flags. Etat par défaut => je viens du cinéma et je créé
        $fromCinema    = true;
        $fromFilm      = false;
        $isItACreation = true;
        $alreadyExists = false;

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
                    [
                'from',
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
                    [
                'datedebut',
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
            // d'où vient on ?
            $from                = $entries['from'];
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
                // je récupère l'objet Cinema
                $cinema        = $app['dao.seance']->getCinemaDAO()->find($entries['cinemaID']);
                // je récupère l'objet Film
                $film          = $app['dao.seance']->getFilmDAO()->find($entries['filmID']);
                // on crée l'objet Seance
                $seance        = new \Semeformation\Mvc\Cinema_crud\models\Seance();
                $seance->setHeureDebut($datetimeDebut);
                $seance->setHeureFin($datetimeFin);
                $seance->setVersion($entries['version']);
                $seance->setCinema($cinema);
                $seance->setFilm($film);
                try {
                    // je sauvegarde l'objet en BDD
                    $app['dao.seance']->save($seance,
                            $entries['dateheuredebutOld'],
                            $entries['dateheurefinOld']);
                    // en fonction d'où je viens, je redirige
                    if (strstr($entries['from'], 'movie')) {
                        // on redirige vers les séances du film
                        return $app->redirect($request->getBasePath() . '/showtime/movie/' . $entries['filmID']);
                    } else {
                        // on redirige vers les séances du cinéma
                        return $app->redirect($request->getBasePath() . '/showtime/cinema/' . $entries['cinemaID']);
                    }
                } catch (BusinessObjectAlreadyExists $ex) {
                    $alreadyExists = true;
                }
            }
        }
        // sinon, on retourne à l'accueil
        else {
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

}
