<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

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
class FavoriteController extends Controller {

    private $prefereDAO;

    /**
     * Constructeur de la classe
     */
    public function __construct(LoggerInterface $logger = null) {
        $this->prefereDAO = new PrefereDAO($logger);
        // Ajout du DAO Utilisateur et Film pour le DAO Prefere
        $this->prefereDAO->setUtilisateurDAO(new UtilisateurDAO($logger));
        $this->prefereDAO->setFilmDAO(new FilmDAO($logger));
    }

    public function editFavoriteMoviesList(Request $request = null,
            Application $app = null) {
        // si l'utilisateur n'est pas connecté
        if (!$app['session']->get('user')) {
            // renvoi à la page d'accueil
            return $app->redirect($request->getBasePath() . '/home');
        }
        // l'utilisateur est loggué
        else {
            $utilisateur = $this->prefereDAO->getUtilisateurDAO()->getUserByEmailAddress($app['session']->get('user')['username']);
        }

        // on récupère la liste des films préférés grâce à l'utilisateur identifié
        $preferes = $this->prefereDAO->getFavoriteMoviesFromUser($utilisateur->getUserId());

        // On génère la vue Films préférés
        $vue = new View("FavoriteMoviesList");
        // En passant les variables nécessaires à son bon affichage
        return $vue->generer($request,
                        array(
                    'utilisateur' => $utilisateur,
                    'preferes'    => $preferes));
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
        // variable qui sert à conditionner l'affichage du formulaire
        $isItACreation   = false;

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


            // si l'action demandée est retour en arrière
            if (!is_null($entries['backToList'])) {
                // redirection vers la liste des préférences de films
                return $app->redirect($request->getBasePath() . '/favorite/list');
            }
            // sinon (l'action demandée est la sauvegarde d'un favori)
            else {
                // si un film a été selectionné 
                if (!is_null($entries['filmID'])) {

                    // et que nous ne sommes pas en train de modifier une préférence
                    if (is_null($entries['modificationInProgress'])) {
                        // on ajoute la préférence de l'utilisateur
                        $this->prefereDAO->insertNewFavoriteMovie($entries['userID'],
                                $entries['filmID'], $entries['comment']);
                    }
                    // sinon, nous sommes dans le cas d'une modification
                    else {
                        // mise à jour de la préférence
                        $this->prefereDAO->updateFavoriteMovie($entries['userID'],
                                $entries['filmID'], $entries['comment']);
                    }
                    // on revient à la liste des préférences
                    // redirection vers la liste des préférences de films
                    return $app->redirect($request->getBasePath() . '/favorite/list');
                }
                // sinon (un film n'a pas été sélectionné)
                else {
                    // 
                    $aFilmIsSelected = false;
                    $isItACreation   = true;
                    $films           = $this->prefereDAO->getFilmDAO()->getMoviesNonAlreadyMarkedAsFavorite($_SESSION['userID']);
                    // initialisation des champs du formulaire
                    $preference      = [
                        "userID"      => $entries["userID"],
                        "filmID"      => "",
                        "titre"       => "",
                        "commentaire" => $entries["comment"]];
                    $userID          = $entries['userID'];
                }
            }
            // sinon (nous sommes en GET) et que l'id du film et l'id du user sont bien renseignés
        } elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {

            // on récupère les données depuis l'URL (transmises en paramètres d'entrée de la fonction
            $entries['filmID'] = $filmId;
            $entries['userID'] = $userId;

            if ($entries && !is_null($entries['filmID']) && $entries['filmID'] !==
                    '' && !is_null($entries['userID']) && $entries['userID'] !==
                    '') {
                // on récupère les informations manquantes (le commentaire afférent)
                $prefere    = $this->prefereDAO->getFavoriteMovieInformations($entries['userID'],
                        $entries['filmID']);
                // TODO : faire autrement qu'avec un vecteur
                $preference = [
                    "userID"      => $app['session']->get('user')['userId'],
                    "filmID"      => $prefere->getFilm()->getFilmId(),
                    "titre"       => $prefere->getFilm()->getTitre(),
                    "commentaire" => $prefere->getCommentaire()];
                // sinon, c'est une création
            } else {
                // C'est une création
                $isItACreation = true;

                $films      = $this->prefereDAO->getFilmDAO()->getMoviesNonAlreadyMarkedAsFavorite($app['session']->get('user')['userId']);
                // on initialise les autres variables de formulaire à vide
                $preference = [
                    "userID"      => $app['session']->get('user')['userId'],
                    "filmID"      => "",
                    "titre"       => "",
                    "commentaire" => ""];
            }
        }

        $donnees = ['aFilmIsSelected' => $aFilmIsSelected,
            'isItACreation'   => $isItACreation,
            'preference'      => $preference,
            'userID'          => $preference['userID'],
            'films'           => $films
        ];
        // On génère la vue Films préférés
        $vue     = new View("FavoriteMovie");
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
            Application $app = null, $userId = null, $filmId = null): RedirectResponse {
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
