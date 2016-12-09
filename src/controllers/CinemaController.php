<?php

namespace Semeformation\Mvc\Cinema_crud\controllers;

use Semeformation\Mvc\Cinema_crud\dao\CinemaDAO;
use Semeformation\Mvc\Cinema_crud\views\View;
use Psr\Log\LoggerInterface;

/**
 * Description of CinemaController
 *
 * @author User
 */
class CinemaController {

    private $cinemaDAO;

    public function __construct(LoggerInterface $logger) {
        $this->cinemaDAO = new CinemaDAO($logger);
    }

    /**
     * Route Liste des cinémas
     */
    public function cinemasList() {
        $isUserAdmin = false;

        session_start();
        // si l'utilisateur est pas connecté et qu'il est amdinistrateur
        if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
            $isUserAdmin = true;
        }
        // on récupère la liste des cinémas ainsi que leurs informations
        $cinemas = $this->cinemaDAO->getCinemasList();

        // On génère la vue films
        $vue = new View("CinemasList");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinemas'     => $cinemas,
            'isUserAdmin' => $isUserAdmin]);
    }

    /**
     * Route Ajouter/Modifier un cinéma
     */
    public function editCinema() {
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

            // on "sainifie" les entrées
            $sanEntries = filter_input_array(INPUT_POST,
                    [
                'backToList'             => FILTER_DEFAULT,
                'cinemaID'               => FILTER_SANITIZE_NUMBER_INT,
                'adresse'                => FILTER_SANITIZE_STRING,
                'denomination'           => FILTER_SANITIZE_STRING,
                'modificationInProgress' => FILTER_SANITIZE_STRING]);

            // si l'action demandée est retour en arrière
            if ($sanEntries['backToList'] !== null) {
                // on redirige vers la page des cinémas
                header('Location: index.php?action=cinemasList');
                exit;
            }
            // sinon (l'action demandée est la sauvegarde d'un cinéma)
            else {

                // et que nous ne sommes pas en train de modifier un cinéma
                if ($sanEntries['modificationInProgress'] == null) {
                    // on ajoute le cinéma
                    $this->cinemaDAO->insertNewCinema($sanEntries['denomination'],
                            $sanEntries['adresse']);
                }
                // sinon, nous sommes dans le cas d'une modification
                else {
                    // mise à jour du cinéma
                    $this->cinemaDAO->updateCinema($sanEntries['cinemaID'],
                            $sanEntries['denomination'], $sanEntries['adresse']);
                }
                // on revient à la liste des cinémas
                header('Location: index.php?action=cinemasList');
                exit;
            }
        }// si la page est chargée avec $_GET
        elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {
            // on "sainifie" les entrées
            $sanEntries = filter_input_array(INPUT_GET,
                    [
                'cinemaID' => FILTER_SANITIZE_NUMBER_INT
            ]);
            if ($sanEntries && $sanEntries['cinemaID'] !== null && $sanEntries['cinemaID'] !==
                    '') {
                // on récupère les informations manquantes 
                $cinema = $this->cinemaDAO->getCinemaByID($sanEntries['cinemaID']);
            }
            // sinon, c'est une création
            else {
                $isItACreation = true;
                $cinema        = null;
            }
        }
        // On génère la vue films
        $vue = new View("EditCinema");
        // En passant les variables nécessaires à son bon affichage
        $vue->generer([
            'cinema'        => $cinema,
            'isItACreation' => $isItACreation,
        ]);
    }

    /**
     * Route supprimer un cinéma
     */
    public function deleteCinema() {
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
            $sanitizedEntries = filter_input_array(INPUT_POST,
                    ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

            // suppression de la préférence de film
            $this->cinemaDAO->deleteCinema($sanitizedEntries['cinemaID']);
        }
        // redirection vers la liste des cinémas
        header("Location: index.php?action=cinemasList");
        exit;
    }

}
