<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . './includes/managers.php';

// si la méthode de formulaire est la méthode GET
if (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "GET") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

    // si l'identifiant du cinéma a bien été passé en GET
    if ($sanitizedEntries && $sanitizedEntries['cinemaID'] !== NULL && $sanitizedEntries['cinemaID'] != '') {
        // on récupère l'identifiant du cinéma
        $cinemaID = $sanitizedEntries['cinemaID'];
        // puis on récupère les informations du cinéma en question
        $cinema = $cinemasMgr->getCinemaInformationsByID($cinemaID);
    }
    // sinon, on retourne à l'accueil
    else {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}

// on récupère la liste des films de ce cinéma
$films = $filmsMgr->getCinemaMoviesByCinemaID($cinemaID);

// On appelle la vue
include __DIR__ . './views/viewCinemaShowtimes.php';
