<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

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
    $sanitizedEntries = filter_input_array(INPUT_POST, ['filmID' => FILTER_SANITIZE_NUMBER_INT]);

    // suppression de la préférence de film
    $filmsMgr->deleteMovie($sanitizedEntries['filmID']);
}
// redirection vers la liste des films
header("Location: moviesList.php");
exit;
