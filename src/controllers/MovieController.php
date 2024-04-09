<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\models\Film;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of MovieController
 *
 * @author User
 */
class MovieController {

    private $film;

    public function __construct(LoggerInterface $logger) {
        $this->film = new Film($logger);
    }

    /**
     * Route Liste des films
     */
    public function moviesList()
    {
        $isUserAdmin = false;

        session_start();
        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
            $isUserAdmin = true;
        }

        // on récupère la liste des films ainsi que leurs informations
        $films = $this->film->getMoviesList();

        // On génère la vue films
        $vue = new View("MoviesList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'films'       => $films,
            'isUserAdmin' => $isUserAdmin]);
    }

    /**
     * Route Supprimer un film
     */
    public function deleteMovie()
    {
        session_start();
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!array_key_exists("user", $_SESSION) or $_SESSION['user'] !== 'admin@adm.adm') {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on "sainifie" les entrées
            $sanitizedEntries = filter_input_array(
                INPUT_POST,
                ['filmID' => FILTER_SANITIZE_NUMBER_INT]
            );

            // suppression de la préférence de film
            $this->film->deleteMovie($sanitizedEntries['filmID']);
        }
        // redirection vers la liste des films
        header("Location: index.php?action=moviesList");
        exit;
    }

    /**
     * Route Ajouter / Modifier un film
     */
    public function editMovie()
    {
        session_start();
        // si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
        if (!array_key_exists("user", $_SESSION) or $_SESSION['user'] !== 'admin@adm.adm') {
            // renvoi à la page d'accueil
            header('Location: index.php');
            exit;
        }

        // variable qui sert à conditionner l'affichage du formulaire
        $isItACreation = false;

        // si la méthode de formulaire est la méthode POST
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

            // on assainit les entrées
            $sanEntries = filter_input_array(
                INPUT_POST,
                [
                'backToList'             => FILTER_DEFAULT,
                'filmID'                 => FILTER_SANITIZE_NUMBER_INT,
                'titre'                  => FILTER_DEFAULT,
                'titreOriginal'          => FILTER_DEFAULT,
                'modificationInProgress' => FILTER_DEFAULT
            ]
            );

            // si l'action demandée est retour en arrière
            if ($sanEntries['backToList'] !== null) {
                // on redirige vers la page des films
                header('Location: index.php?action=moviesList');
                exit;
            }
            // sinon (l'action demandée est la sauvegarde d'un film)
            else {

                // et que nous ne sommes pas en train de modifier un film
                if ($sanEntries['modificationInProgress'] == null) {
                    // on ajoute le film
                    $this->film->insertNewMovie(
                        $sanEntries['titre'],
                        $sanEntries['titreOriginal']
                    );
                }
                // sinon, nous sommes dans le cas d'une modification
                else {
                    // mise à jour du film
                    $this->film->updateMovie(
                        $sanEntries['filmID'],
                        $sanEntries['titre'],
                        $sanEntries['titreOriginal']
                    );
                }
                // on revient à la liste des films
                header('Location: index.php?action=moviesList');
                exit;
            }
        }// si la page est chargée avec $_GET
        elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {
            // on assainit les entrées
            $sanEntries = filter_input_array(
                INPUT_GET,
                ['filmID' => FILTER_SANITIZE_NUMBER_INT]
            );
            if ($sanEntries && $sanEntries['filmID'] !== null && $sanEntries['filmID'] !==
                    '') {
                // on récupère les informations manquantes
                $film = $this->film->getMovieInformationsByID($sanEntries['filmID']);
            }
            // sinon, c'est une création
            else {
                $isItACreation = true;
                $film = [
                    'FILMID' => '',
                    'TITRE' => '',
                    'TITREORIGINAL' => ''
                ];
            }
        }

        // On génère la vue films
        $vue = new View("EditMovie");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'film'          => $film,
            'isItACreation' => $isItACreation]);
    }
}
