<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . './includes/managers.php';

session_start();
// si l'utilisateur n'est pas connecté
if (!array_key_exists("user",
                $_SESSION)) {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// variable de contrôle de formulaire
$aFilmIsSelected = true;
// variable qui sert à conditionner l'affichage du formulaire
$isItACreation = false;

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "POST") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_POST,
            ['backToList' => FILTER_DEFAULT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'userID' => FILTER_SANITIZE_NUMBER_INT,
        'comment' => FILTER_SANITIZE_STRING,
        'modificationInProgress' => FILTER_SANITIZE_STRING]);

    // si l'action demandée est retour en arrière
    if ($sanitizedEntries['backToList'] !== NULL) {
        // on redirige vers la page d'édition des films favoris
        header('Location: editFavoriteMoviesList.php');
        exit;
    }
    // sinon (l'action demandée est la sauvegarde d'un favori)
    else {
        // si un film a été selectionné 
        if ($sanitizedEntries['filmID'] !== NULL) {

            // et que nous ne sommes pas en train de modifier une préférence
            if ($sanitizedEntries['modificationInProgress'] == NULL) {
                // on ajoute la préférence de l'utilisateur
                $managers['preferesMgr']->insertNewFavoriteMovie($sanitizedEntries['userID'],
                        $sanitizedEntries['filmID'],
                        $sanitizedEntries['comment']);
            }
            // sinon, nous sommes dans le cas d'une modification
            else {
                // mise à jour de la préférence
                $managers['preferesMgr']->updateFavoriteMovie($sanitizedEntries['userID'],
                        $sanitizedEntries['filmID'],
                        $sanitizedEntries['comment']);
            }
            // on revient à la liste des préférences
            header('Location: editFavoriteMoviesList.php');
            exit;
        }
        // sinon (un film n'a pas été sélectionné)
        else {
            // 
            $aFilmIsSelected = false;
            $isItACreation = true;
            $films = $managers['preferesMgr']->getMoviesNonAlreadyMarkedAsFavorite($_SESSION['userID']);
            // initialisation des champs du formulaire
            $preference = [
                "userID" => $sanitizedEntries["userID"],
                "filmID" => "",
                "titre" => "",
                "commentaire" => $sanitizedEntries["comment"]];
            $userID = $sanitizedEntries['userID'];
        }
    }
// sinon (nous sommes en GET) et que l'id du film et l'id du user sont bien renseignés
} elseif (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "GET") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT,
        'userID' => FILTER_SANITIZE_NUMBER_INT]);

    if ($sanitizedEntries && $sanitizedEntries['filmID'] !== NULL && $sanitizedEntries['filmID'] !== '' && $sanitizedEntries['userID'] !== NULL && $sanitizedEntries['userID'] !== '') {
        // on récupère les informations manquantes (le commentaire afférent)
        $preference = $managers['preferesMgr']->getFavoriteMovieInformations($sanitizedEntries['userID'],
                $sanitizedEntries['filmID']);
        // sinon, c'est une création
    } else {
        // C'est une création
        $isItACreation = true;

        $films = $managers['preferesMgr']->getMoviesNonAlreadyMarkedAsFavorite($_SESSION['userID']);
        // on initialise les autres variables de formulaire à vide
        $preference = [
            "userID" => $_SESSION['userID'],
            "filmID" => "",
            "titre" => "",
            "commentaire" => ""];
    }
}

// on inclut la vue correspondante
include __DIR__ . './views/viewFavoriteMovie.php';
