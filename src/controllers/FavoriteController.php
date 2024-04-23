<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\controllers\Controller;
use Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\PrefereDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

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
    public function __construct(LoggerInterface $logger)
    {
        $this->prefereDAO = new PrefereDAO($logger);
        // Ajout du DAO Utilisateur et Film pour le DAO Prefere
        $this->prefereDAO->setUtilisateurDAO(new UtilisateurDAO($logger));
        $this->prefereDAO->setFilmDAO(new FilmDAO($logger));
    }

    public function editFavoriteMoviesList($addMode = "")
    {
        $this->redirectIfNotNotConnected();
        // l'utilisateur est loggué
        $utilisateur = $this->prefereDAO->getUtilisateurDAO()->getUserByEmailAddress($_SESSION['user']);
        // on récupère la liste des films préférés grâce à l'utilisateur identifié
        $preferences = $this->prefereDAO->getFavoriteMoviesFromUser($utilisateur->getUserId());
        $preferenceToBeModified = [];
        $toBeModified = null;
        // on récupère la liste des films non marqués comme ayant un commentaire
        $films = $this->prefereDAO->getFilmDAO()->getMoviesNonAlreadyMarkedAsFavorite($utilisateur->getUserId());
        // si nous sommes en mode modification
        if ($addMode === "edit") {
            $sanitizedEntries = filter_input_array(
                INPUT_GET,
                ['filmID' => FILTER_SANITIZE_NUMBER_INT]
            );
            // on a besoin de récupérer la préférence de film à partir de l'utilisateur et de l'identifiant du film
            $preferenceToBeModified = $this->prefereDAO->getFavoriteMovieInformations($utilisateur->getUserId(), $sanitizedEntries['filmID']);
            $toBeModified = $preferenceToBeModified->getFilm()->getFilmId();
        }

        // On génère la vue Films préférés
        $vue = new View("FavoriteMoviesList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(
            [
                'utilisateur'            => $utilisateur,
                'preferences'            => $preferences,
                'preferenceToBeModified' => $preferenceToBeModified,
                'addMode'                => $addMode,
                'films'                  => $films,
                'toBeModified'           => $toBeModified,
            ]
        );
    }

    public function editFavoriteMovie()
    {
        $this->redirectIfNotNotConnected();

        $films = null;
        // variable de contrôle de formulaire
        $noneSelected = true;

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
            // on assainit les entrées
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                [
                    'backToList'             => FILTER_DEFAULT,
                    'filmID'                 => FILTER_SANITIZE_NUMBER_INT,
                    'userID'                 => FILTER_SANITIZE_NUMBER_INT,
                    'comment'                => FILTER_DEFAULT,
                    'modificationInProgress' => FILTER_DEFAULT,
                ]
            );

            $utilisateur = $this->prefereDAO->getUtilisateurDAO()->getUserByEmailAddress($_SESSION['user']);

            // si un film a été selectionné
            if ($sanitizedEntries['filmID'] !== null && $sanitizedEntries['filmID'] !== "") {
                // et que nous ne sommes pas en train de modifier une préférence
                if ($sanitizedEntries['modificationInProgress'] === null) {
                    // on ajoute la préférence de l'utilisateur
                    $this->prefereDAO->insertNewFavoriteMovie(
                        $sanitizedEntries['userID'],
                        $sanitizedEntries['filmID'],
                        $sanitizedEntries['comment']
                    );
                } else { // sinon, nous sommes dans le cas d'une modification
                    // mise à jour de la préférence
                    $this->prefereDAO->updateFavoriteMovie(
                        $sanitizedEntries['userID'],
                        $sanitizedEntries['filmID'],
                        $sanitizedEntries['comment']
                    );
                }
                // on revient à la liste des préférences
                // redirection vers la liste des préférences de films
                header("Location: index.php?action=editFavoriteMoviesList");
                exit;
            } else { // sinon (un film n'a pas été sélectionné)
                // on récupère la listes des films non marqués comme favoris
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
        $vue->generer($donnees);
    }

    public function deleteFavoriteMovie()
    {
        // on assainit les entrées
        $sanitizedEntries = filter_input_array(
            INPUT_POST,
            [
                'userID' => FILTER_SANITIZE_NUMBER_INT,
                'filmID' => FILTER_SANITIZE_NUMBER_INT,
            ]
        );

        // suppression de la préférence de film
        $this->prefereDAO->deleteFavoriteMovie(
            $sanitizedEntries['userID'],
            $sanitizedEntries['filmID']
        );
        // redirection vers la liste des préférences de films
        header("Location: index.php?action=editFavoriteMoviesList");
        exit;
    }
}
