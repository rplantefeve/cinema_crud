<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\views\View;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Psr\Log\LoggerInterface;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController extends Controller {

    public function __construct(LoggerInterface $logger = null) {
    }

    /**
     * Route Liste des films
     */
    function moviesList(Request $request = null, Application $app = null) {
        $isUserAdmin = false;

        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if ($app['session']->get('user') and $app['session']->get('user')['username'] ==
                'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on récupère la liste des films ainsi que leurs informations
        $films = $app['dao.film']->findAll();

        // On génère la vue films
        $vue = new View("MoviesList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
                [
            'films'       => $films,
            'isUserAdmin' => $isUserAdmin]);
    }

    /**
     * Route Ajouter / Modifier un film
     */
    function editMovie(Request $request = null, Application $app = null,
            string $filmId = null) {
        
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!$app['session']->get('user') or $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // variable qui sert à conditionner l'affichage du formulaire
        $isItACreation = false;

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on assainit les entrées
            $entries = $this->extractArrayFromPostRequest($request,
                    [
                'backToList',
                'titre',
                'titreOriginal',
                'modificationInProgress'
            ]);

            // si l'action demandée est retour en arrière
            if ($entries['backToList'] !== null) {
                // on redirige vers la page des films
                return $app->redirect($request->getBasePath() . '/movie/list');
            }
            // sinon (l'action demandée est la sauvegarde d'un film)
            else {

                // et que nous ne sommes pas en train de modifier un film
                if ($entries['modificationInProgress'] == null) {
                    // on ajoute le film
                    $app['dao.film']->insertNewMovie($entries['titre'],
                            $entries['titreOriginal']);
                }
                // sinon, nous sommes dans le cas d'une modification
                else {
                    // mise à jour du film
                    $app['dao.film']->updateMovie($filmId,
                            $entries['titre'], $entries['titreOriginal']);
                }
                // on revient à la liste des films
                return $app->redirect($request->getBasePath() . '/movie/list');
            }
        }// si la page est chargée avec $_GET
        elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {
            // on assainit les entrées
            $entries['filmID'] = $filmId;
            if ($entries && $entries['filmID'] !== null && $entries['filmID'] !==
                    '') {
                // on récupère les informations manquantes 
                $film = $app['dao.film']->find($entries['filmID']);
            }
            // sinon, c'est une création
            else {
                $isItACreation = true;
                $film          = null;
            }
        }

        // On génère la vue films
        $vue = new View("EditMovie");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request, [
            'film'          => $film,
            'isItACreation' => $isItACreation]);
    }

    /**
     * Route Supprimer un film
     * @param string $filmId
     * @param Request $request
     * @param Application $app
     * @return type
     */
    public function deleteMovie(string $filmId, Request $request = null,
            Application $app = null) {
        
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas administrateur
        if (!$app['session']->get('user') or $app['session']->get('user')['username'] !==
                'admin@adm.adm') {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on assainit les entrées
            $entries['filmID'] = $filmId;

            // suppression de la préférence de film
            $app['dao.film']->delete($entries['filmID']);
        }
        // redirection vers la liste des films
        return $app->redirect($request->getBasePath() . '/movie/list');
    }

}
