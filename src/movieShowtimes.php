<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

session_start();
// si l'utilisateur admin est connexté
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $adminConnected = true;
}

// si la méthode de formulaire est la méthode GET
if (filter_input(
    INPUT_SERVER,
                'REQUEST_METHOD'
) === "GET") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(
        INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT]
    );
    // si l'identifiant du film a bien été passé en GET'
    if ($sanitizedEntries && $sanitizedEntries['filmID'] !== null && $sanitizedEntries['filmID'] !== '') {
        // on récupère l'identifiant du cinéma
        $filmID = $sanitizedEntries['filmID'];
        // puis on récupère les informations du film en question
        $film = $managers['filmsMgr']->getMovieInformationsByID($filmID);
        $cinemasUnplanned = $managers['filmsMgr']->getNonPlannedCinemas($filmID);
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

// on récupère la liste des cinémas de ce film
$cinemas = $managers['cinemasMgr']->getMovieCinemasByMovieID($filmID);

// on inclut la vue correspondante
include __DIR__ . '/views/viewMovieShowtimes.php';
