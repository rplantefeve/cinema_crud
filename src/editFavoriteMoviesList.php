<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

session_start();
// si l'utilisateur n'est pas connecté
if (array_key_exists(
    "user",
    $_SESSION
) === false
) {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
} else { // l'utilisateur est loggué
    $utilisateur = $utilisateursMgr->getCompleteUsernameByEmailAddress($_SESSION['user']);
}

// on récupère la liste des films préférés grâce à l'utilisateur identifié
$films = $preferesMgr->getFavoriteMoviesFromUser($utilisateur['userID']);

// on inclut la vue correspondante
require __DIR__ . '/views/viewFavoriteMoviesList.php';
