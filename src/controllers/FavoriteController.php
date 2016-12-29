<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\models\Prefere;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of FavoriteController
 *
 * @author User
 */
class FavoriteController extends Controller {

    public function editFavoriteMoviesList(Request $request = null,
            Application $app = null) {
        // si l'utilisateur n'est pas connecté
        if (!$app['session']->get('user')) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }
        // l'utilisateur est loggué
        else {
            $utilisateur = $app['dao.prefere']->getUtilisateurDAO()->findOneByCourriel($app['session']->get('user')['username']);
        }

        // on récupère la liste des films préférés grâce à l'utilisateur identifié
        $preferes = $app['dao.prefere']->findAllByUserId($utilisateur->getUserId());

        $donnees = [
            'titre'       => 'Préférences de film',
            'utilisateur' => $utilisateur,
            'preferes'    => $preferes];

        // On génère la vue Films préférés
        return $app['twig']->render('favorites.html.twig', $donnees);
    }

    public function editFavoriteMovie(Request $request = null,
            Application $app = null, $userId = null, $filmId = null) {
        // si l'utilisateur n'est pas connecté
        if (!$app['session']->get('user')) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        $films           = null;
        // variable de contrôle de formulaire
        $aFilmIsSelected = true;
        $prefere         = null;

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST')) {

            // on extrait les données post de la requête
            $entries = $this->extractArrayFromPostRequest($request,
                    [
                'backToList',
                'filmID',
                'userID',
                'comment']);


            // si l'action demandée est retour en arrière
            if (!is_null($entries['backToList'])) {
                // redirection vers la liste des préférences de films
                return $app->redirect($request->getBasePath() . '/favorite/list');
            }
            // sinon (l'action demandée est la sauvegarde d'un favori)
            else {
                // on crée la préférence 
                $prefere     = new Prefere();
                // on récupère l'utilisateur
                $utilisateur = $app['dao.prefere']->getUtilisateurDAO()->find($entries['userID']);
                $prefere->setUtilisateur($utilisateur);
                $prefere->setCommentaire($entries['comment']);
                // si un film a été selectionné 
                if (!is_null($entries['filmID'])) {
                    // On récupère le film
                    $film = $app['dao.prefere']->getFilmDAO()->find($entries['filmID']);
                    // on hydrate l'objet
                    $prefere->setFilm($film);
                    // on sauvegarde l'objet métier en BDD
                    $app['dao.prefere']->save($prefere);
                    // redirection vers la liste des préférences de films
                    return $app->redirect($request->getBasePath() . '/favorite/list');
                }
                // sinon (un film n'a pas été sélectionné)
                else {
                    // 
                    $aFilmIsSelected = false;
                    $films           = $app['dao.prefere']->getFilmDAO()->findAllByUserIdNotIn($app['session']->get('user')['userId']);
                }
            }
            // sinon (nous sommes en GET) et que l'id du film et l'id du user sont bien renseignés
        } elseif ($request->isMethod('GET')) {

            // on récupère les données depuis l'URL (transmises en paramètres d'entrée de la fonction
            $entries['filmID'] = $filmId;
            $entries['userID'] = $userId;

            if (is_null($entries['userID'])) {
                $entries['userID'] = $app['session']->get('user')['userId'];
            }

            if ($entries && !is_null($entries['filmID']) && $entries['filmID'] !==
                    '' && !is_null($entries['userID']) && $entries['userID'] !==
                    '') {
                // on récupère les informations manquantes (le commentaire afférent)
                $prefere    = $app['dao.prefere']->find($entries['userID'],
                        $entries['filmID']);
                // TODO : faire autrement qu'avec un vecteur
                $preference = [
                    "userID"      => $app['session']->get('user')['userId'],
                    "filmID"      => $prefere->getFilm()->getFilmId(),
                    "titre"       => $prefere->getFilm()->getTitre(),
                    "commentaire" => $prefere->getCommentaire()];
                // sinon, c'est une création
            } else {
                $films       = $app['dao.prefere']->getFilmDAO()->findAllByUserIdNotIn($entries['userID']);
                // on initialise les autres variables de formulaire à vide
                $prefere     = new Prefere();
                $utilisateur = $app['dao.prefere']->getUtilisateurDAO()->find($entries['userID']);
                $prefere->setUtilisateur($utilisateur);
            }
        }

        $donnees = [
            'titre'           => 'Ajouter/Modifier une préférence',
            'aFilmIsSelected' => $aFilmIsSelected,
            'prefere'         => $prefere,
            'userID'          => $entries['userID'],
            'films'           => $films
        ];
        // On génère la vue Films préférés
        return $app['twig']->render('favorite.edit.html.twig', $donnees);
    }

    /**
     * Supprime une préférence de film
     * @param Request $request
     * @param Application $app
     * @param int $userId
     * @param int $filmId
     * @return RedirectResponse
     */
    public function deleteFavoriteMovie(Request $request = null,
            Application $app = null, $userId = null, $filmId = null): RedirectResponse {
        // si l'utilisateur n'est pas connecté
        if (!$app['session']->get('user')) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // suppression de la préférence de film
        $app['dao.prefere']->delete($userId, $filmId);
        // redirection vers la liste des préférences de films
        return $app->redirect($request->getBasePath() . '/favorite/list');
    }

}
