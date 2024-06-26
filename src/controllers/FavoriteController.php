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
class FavoriteController extends Controller
{
    public function editFavoriteMoviesList(
        Request $request = null,
        Application $app = null,
        $addMode = "",
        $filmId = null
    ) {
        // si l'utilisateur n'est pas connecté
        $this->redirectIfUserNotConnected($request, $app);
        // l'utilisateur est loggué
        $utilisateur = $app['dao.prefere']->getUtilisateurDAO()->findOneByCourriel($app['session']->get('user')['username']);

        // on récupère la liste des films préférés grâce à l'utilisateur identifié
        $preferences = $app['dao.prefere']->findAllByUserId($utilisateur->getUserId());
        $preferenceToBeModified = [];
        $toBeModified = null;
        // on récupère la liste des films non marqués comme ayant un commentaire QUE SI NOUS SOMMES EN AJOUT !
        $films = $app['dao.prefere']->getFilmDAO()->findAllByUserIdNotIn($utilisateur->getUserId());
        // si nous sommes en mode modification
        if ($addMode === "edit") {
            // on a besoin de récupérer le commentaire à partir du film
            $preferenceToBeModified = $app['dao.prefere']->find($utilisateur->getUserId(), $filmId);
            $toBeModified = $preferenceToBeModified->getFilm()->getFilmId();
        }

        $donnees = [
            'titre'                  => 'Préférences de film',
            'utilisateur'            => $utilisateur,
            'preferences'            => $preferences,
            'preferenceToBeModified' => $preferenceToBeModified,
            'addMode'                => $addMode,
            'filmsNotCommented'      => $films,
            'toBeModified'           => $toBeModified,
        ];

        return $app['twig']->render('favorites.html.twig', $donnees);
    }

    /**
     * Undocumented function
     *
     * @param Request|null $request
     * @param Application|null $app
     * @return RedirectResponse|string
     */
    public function editFavoriteMovie(Request $request = null, Application $app = null)
    {
        // si l'utilisateur n'est pas connecté
        $this->redirectIfUserNotConnected($request, $app);

        $films = null;
        // variable de contrôle de formulaire
        $prefere = null;
        $noneSelected = true;

        // si la méthode de formulaire est la méthode POST
        if ($request->isMethod('POST') === true) {
            // on extrait les données post de la requête
            $entries = $this->extractArrayFromPostRequest(
                $request,
                [
                    'backToList',
                    'filmID',
                    'userID',
                    'comment',
                    'modificationInProgress',
                ]
            );

            $utilisateur = $app['dao.prefere']->getUtilisateurDAO()->find($entries['userID']);

            // si un film a été selectionné
            if ($entries['filmID'] !== null && $entries['filmID'] !== "") {
                // on crée la préférence
                $prefere = new Prefere();
                // on récupère l'utilisateur
                $utilisateur = $app['dao.prefere']->getUtilisateurDAO()->find($entries['userID']);
                $prefere->setUtilisateur($utilisateur);
                $prefere->setCommentaire($entries['comment']);
                // On récupère le film
                $film = $app['dao.prefere']->getFilmDAO()->find($entries['filmID']);
                // on hydrate l'objet
                $prefere->setFilm($film);
                // on sauvegarde l'objet métier en BDD
                $app['dao.prefere']->save($prefere);
                // redirection vers la liste des préférences de films
                return $app->redirect($request->getBasePath() . '/favorite/list');
            } else { // sinon (un film n'a pas été sélectionné)
                // on récupère la liste des films non commentés
                $films = $app['dao.prefere']->getFilmDAO()->findAllByUserIdNotIn($utilisateur->getUserId());
                // et la listes des films favoris
                $preferences = $app['dao.prefere']->findAllByUserId($utilisateur->getUserId());
            }

            $donnees = [
                'titre'             => 'Ajouter/Modifier une préférence',
                'userID'            => $entries['userID'],
                'filmsNotCommented' => $films,
                'noneSelected'      => $noneSelected,
                'addMode'           => "add",
                'preferences'       => $preferences,
                'utilisateur'       => $utilisateur,
            ];
            // On génère la vue Films préférés
            return $app['twig']->render('favorites.html.twig', $donnees);
        }
    }

    /**
     * Supprime une préférence de film
     * @param Request $request
     * @param Application $app
     * @param string $userId
     * @param string $filmId
     * @return RedirectResponse
     */
    public function deleteFavoriteMovie(
        Request $request = null,
        Application $app = null,
        $userId = null,
        $filmId = null
    ) {
        // si l'utilisateur n'est pas connecté
        $this->redirectIfUserNotConnected($request, $app);

        // suppression de la préférence de film
        $app['dao.prefere']->delete($userId, $filmId);
        // redirection vers la liste des préférences de films
        return $app->redirect($request->getBasePath() . '/favorite/list');
    }
}
