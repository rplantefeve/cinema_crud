<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

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
class FavoriteController {

    private $prefereDAO;

    /**
     * Constructeur de la classe
     */
    public function __construct(LoggerInterface $logger) {
        $this->prefereDAO = new PrefereDAO($logger);
        // Ajout du DAO Utilisateur et Film pour le DAO Prefere
        $this->prefereDAO->setUtilisateurDAO(new UtilisateurDAO($logger));
        $this->prefereDAO->setFilmDAO(new FilmDAO($logger));
    }

    public function editFavoriteMoviesList() {
        session_start();
        // si l'utilisateur n'est pas connecté
        if (!array_key_exists("user",
                        $_SESSION)) {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }
        // l'utilisateur est loggué
        else {
            $utilisateur = $this->prefereDAO->getUtilisateurDAO()->getUserByEmailAddress($_SESSION['user']);
        }

        // on récupère la liste des films préférés grâce à l'utilisateur identifié
        $preferes = $this->prefereDAO->getFavoriteMoviesFromUser($utilisateur->getUserId());

        // On génère la vue Films préférés
        $vue = new View("FavoriteMoviesList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer(array(
            'utilisateur' => $utilisateur,
            'preferes' => $preferes));
    }

    public function editFavoriteMovie() {
        session_start();
        // si l'utilisateur n'est pas connecté
        if (!array_key_exists("user",
                        $_SESSION)) {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }

        $films = null;
        // variable de contrôle de formulaire
        $aFilmIsSelected = true;
        // variable qui sert à conditionner l'affichage du formulaire
        $isItACreation = false;

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER,
                        'REQUEST_METHOD') === "POST") {

            // on assainit les entrées
            $sanitizedEntries = filter_input_array(INPUT_POST,
                    ['backToList' => FILTER_DEFAULT,
                'filmID' => FILTER_SANITIZE_NUMBER_INT,
                'userID' => FILTER_SANITIZE_NUMBER_INT,
                'comment' => FILTER_DEFAULT,
                'modificationInProgress' => FILTER_DEFAULT]);

            // si l'action demandée est retour en arrière
            if (!is_null($sanitizedEntries['backToList'])) {
                // redirection vers la liste des préférences de films
                header("Location: index.php?action=editFavoriteMoviesList");
                exit;
            }
            // sinon (l'action demandée est la sauvegarde d'un favori)
            else {
                // si un film a été selectionné 
                if (!is_null($sanitizedEntries['filmID'])) {

                    // et que nous ne sommes pas en train de modifier une préférence
                    if (is_null($sanitizedEntries['modificationInProgress'])) {
                        // on ajoute la préférence de l'utilisateur
                        $this->prefereDAO->insertNewFavoriteMovie($sanitizedEntries['userID'],
                                $sanitizedEntries['filmID'],
                                $sanitizedEntries['comment']);
                    }
                    // sinon, nous sommes dans le cas d'une modification
                    else {
                        // mise à jour de la préférence
                        $this->prefereDAO->updateFavoriteMovie($sanitizedEntries['userID'],
                                $sanitizedEntries['filmID'],
                                $sanitizedEntries['comment']);
                    }
                    // on revient à la liste des préférences
                    // redirection vers la liste des préférences de films
                    header("Location: index.php?action=editFavoriteMoviesList");
                    exit;
                }
                // sinon (un film n'a pas été sélectionné)
                else {
                    // 
                    $aFilmIsSelected = false;
                    $isItACreation = true;
                    $films = $this->prefereDAO->getFilmDAO()->getMoviesNonAlreadyMarkedAsFavorite($_SESSION['userID']);
                    // initialisation des champs du formulaire
                    $preference = [
                        "userID" => $sanitizedEntries["userID"],
                        "filmID" => "",
                        "titre" => "",
                        "commentaire" => $sanitizedEntries["comment"]];
                }
            }
            // sinon (nous sommes en GET) et que l'id du film et l'id du user sont bien renseignés
        } elseif (filter_input(INPUT_SERVER,
                        'REQUEST_METHOD') === "GET") {

            // on assainit les entrées
            $sanitizedEntries = filter_input_array(INPUT_GET,
                    ['filmID' => FILTER_SANITIZE_NUMBER_INT,
                'userID' => FILTER_SANITIZE_NUMBER_INT]);

            if ($sanitizedEntries && !is_null($sanitizedEntries['filmID']) && $sanitizedEntries['filmID'] !== '' && !is_null($sanitizedEntries['userID']) && $sanitizedEntries['userID'] !== '') {
                // on récupère les informations manquantes (le commentaire afférent)
                $prefere = $this->prefereDAO->getFavoriteMovieInformations($sanitizedEntries['userID'],
                        $sanitizedEntries['filmID']);
                // TODO : faire autrement qu'avec un vecteur
                $preference = [
                    "userID" => $_SESSION['userID'],
                    "filmID" => $prefere->getFilm()->getFilmId(),
                    "titre" => $prefere->getFilm()->getTitre(),
                    "commentaire" => $prefere->getCommentaire()];
                // sinon, c'est une création
            } else {
                // C'est une création
                $isItACreation = true;

                $films = $this->prefereDAO->getFilmDAO()->getMoviesNonAlreadyMarkedAsFavorite($_SESSION['userID']);
                // on initialise les autres variables de formulaire à vide
                $preference = [
                    "userID" => $_SESSION['userID'],
                    "filmID" => "",
                    "titre" => "",
                    "commentaire" => ""];
            }
        }

        $donnees = ['aFilmIsSelected' => $aFilmIsSelected,
            'isItACreation' => $isItACreation,
            'preference' => $preference,
            'userID' => $preference['userID'],
            'films' => $films
        ];
        // On génère la vue Films préférés
        $vue = new View("FavoriteMovie");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer($donnees);
    }

    public function deleteFavoriteMovie() {
        // on assainit les entrées
        $sanitizedEntries = filter_input_array(INPUT_POST,
                ['userID' => FILTER_SANITIZE_NUMBER_INT,
            'filmID' => FILTER_SANITIZE_NUMBER_INT]);

        // suppression de la préférence de film
        $this->prefereDAO->deleteFavoriteMovie($sanitizedEntries['userID'],
                $sanitizedEntries['filmID']);
        // redirection vers la liste des préférences de films
        header("Location: index.php?action=editFavoriteMoviesList");
        exit;
    }

}
