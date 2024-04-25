<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\Controller;
use Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\PrefereDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Silex\Application;

/**
 * Description of FavoriteController
 *
 * @author User
 */
class FavoriteController extends Controller
{
    private $prefereDAO;

    /**
     * Constructeur de la classe
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->prefereDAO = new PrefereDAO($logger);
        // Ajout du DAO Utilisateur et Film pour le DAO Prefere
        $this->prefereDAO->setUtilisateurDAO(new UtilisateurDAO($logger));
        $this->prefereDAO->setFilmDAO(new FilmDAO($logger));
    }

    public function editFavoriteMoviesList(Request $request = null,
            Application $app = null, $addMode = "", $filmId = null) {
        // si l'utilisateur n'est pas connecté
        if (!$app['session']->get('user')) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }
        // l'utilisateur est loggué
        $utilisateur = $this->prefereDAO->getUtilisateurDAO()->getUserByEmailAddress($app['session']->get('user')['username']);

        // on récupère la liste des films préférés grâce à l'utilisateur identifié
        $preferences = $this->prefereDAO->getFavoriteMoviesFromUser($utilisateur->getUserId());
        $preferenceToBeModified = [];
        $toBeModified = null;
        // on récupère la liste des films non marqués comme ayant un commentaire QUE SI NOUS SOMMES EN AJOUT !
        $films = $this->prefereDAO->getFilmDAO()->getMoviesNonAlreadyMarkedAsFavorite($utilisateur->getUserId());
        // si nous sommes en mode modification
        if($addMode === "edit")
        {
            // on a besoin de récupérer le commentaire à partir du film
            $preferenceToBeModified = $this->prefereDAO->getFavoriteMovieInformations($utilisateur->getUserId(), $filmId);
            $toBeModified = $preferenceToBeModified->getFilm()->getFilmId();
        }

        // On génère la vue Films préférés
        $vue = new View("FavoriteMoviesList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
                        [
                    'utilisateur'            => $utilisateur,
                    'preferences'            => $preferences,
                    'preferenceToBeModified' => $preferenceToBeModified,
                    'addMode'                => $addMode,
                    'films'                 => $films,
                    'toBeModified'          => $toBeModified,
                        ]);
    }

    public function editFavoriteMovie(Request $request = null,
            Application $app = null, $filmId = null) {
        // si l'utilisateur n'est pas connecté
        if (!$app['session']->get('user')) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        $films = null;
        // variable de contrôle de formulaire
        $noneSelected = true;

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on extrait les données post de la requête
            $entries = $this->extractArrayFromPostRequest($request,
                    [
                'backToList',
                'filmID',
                'userID',
                'comment',
                'modificationInProgress']);


                $utilisateur = $this->prefereDAO->getUtilisateurDAO()->getUserByEmailAddress($app['session']->get('user')['username']);

            // si un film a été selectionné 
            if ($entries['filmID'] !== null && $entries['filmID'] !== "") {
                // et que nous ne sommes pas en train de modifier une préférence
                if ($entries['modificationInProgress'] === null) {
                    // on ajoute la préférence de l'utilisateur
                    $this->prefereDAO->insertNewFavoriteMovie($entries['userID'],
                            $entries['filmID'],
                            $entries['comment']);
                }
                // sinon, nous sommes dans le cas d'une modification
                else {
                    // mise à jour de la préférence
                    $this->prefereDAO->updateFavoriteMovie($entries['userID'],
                            $entries['filmID'],
                            $entries['comment']);
                }
                // on revient à la liste des préférences
                // redirection vers la liste des préférences de films
                return $app->redirect($request->getBasePath() . '/favorite/list');
            }
            // sinon (un film n'a pas été sélectionné)
            else {
                
                $films = $this->prefereDAO->getFilmDAO()->getMoviesNonAlreadyMarkedAsFavorite($utilisateur->getUserId());
                // et la listes des films favoris
                $preferences = $this->prefereDAO->getFavoriteMoviesFromUser($utilisateur->getUserId());
            }
        }

        $donnees = [
            'utilisateur'  => $utilisateur,
            'preferences'  => $preferences,
            'films'        => $films,
            'addMode'      => "add",
            'noneSelected' => $noneSelected,
        ];
        // On génère la vue Films préférés
        $vue = new View("FavoriteMoviesList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request, $donnees);
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
            Application $app = null, $userId = null, $filmId = null) {
        // si l'utilisateur n'est pas connecté
        if (!$app['session']->get('user')) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }

        // suppression de la préférence de film
        $this->prefereDAO->deleteFavoriteMovie($userId, $filmId);
        // redirection vers la liste des préférences de films
        return $app->redirect($request->getBasePath() . '/favorite/list');
    }
}
