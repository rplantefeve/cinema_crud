<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Symfony\Component\HttpFoundation\Request;
use Semeformation\Mvc\Cinema_crud\models\Film;
use Silex\Application;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController extends Controller {

    /**
     * Route Liste des films
     */
    public function moviesList(Request $request = null, Application $app = null) {
        $isUserAdmin = false;

        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if ($app['session']->get('user') && $app['session']->get('user')['username'] ==
                'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on récupère la liste des films ainsi que leurs informations
        $films = $app['dao.film']->findAll();

        // Données de la vue
        $donnees = [
            'titre'       => 'Films',
            'films'       => $films,
            'isUserAdmin' => $isUserAdmin];
        // On génère la vue films
        return $app['twig']->render('movies.html.twig', $donnees);
    }

    /**
     * Route Ajouter / Modifier un film
     * @param Request $request
     * @param Application $app
     * @param string $filmId
     * @return string La vue générée
     */
    public function editMovie(Request $request = null, Application $app = null,
            string $filmId = null) {

        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!$app['session']->get('user') || $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest($request,
                    [
                'backToList',
                'titre',
                'titreOriginal',
            ]);

            // si l'action demandée est retour en arrière
            if ($entries['backToList'] !== null) {
                // on redirige vers la page des films
                return $app->redirect($request->getBasePath() . '/movie/list');
            }
            // sinon (l'action demandée est la sauvegarde d'un film)
            else {

                $film = new Film();
                $film->setTitre($entries['titre']);
                $film->setTitreOriginal($entries['titreOriginal']);
                $film->setFilmId($filmId);
                // on sauvegarde le film
                $app['dao.film']->save($film);
                // on revient à la liste des films
                return $app->redirect($request->getBasePath() . '/movie/list');
            }
        }// si la page est chargée avec $_GET
        elseif ($request->isMethod('GET')) {
            // on assainit les entrées
            $entries['filmID'] = $filmId;
            if ($entries && $entries['filmID'] !== null && $entries['filmID'] !==
                    '') {
                // on récupère les informations manquantes 
                $film = $app['dao.film']->find($entries['filmID']);
            }
            // sinon, c'est une création
            else {
                $film = null;
            }
        }

        $donnees = [
            'titre' => 'Ajouter/Modifier un film',
            'film'  => $film];

        // On génère la vue films
        return $app['twig']->render('movie.edit.html.twig', $donnees);
    }

    /**
     * Route Supprimer un film
     * @param string $filmId
     * @param Request $request
     * @param Application $app
     * @return string La vue générée
     */
    public function deleteMovie(string $filmId, Request $request = null,
            Application $app = null) {

        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas administrateur
        if (!$app['session']->get('user') || $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on assainit les entrées
            $entries['filmID'] = $filmId;

            // suppression de la préférence de film
            $app['dao.film']->delete($entries['filmID']);
        }
        // redirection vers la liste des films
        return $app->redirect($request->getBasePath() . '/movie/list');
    }

}
