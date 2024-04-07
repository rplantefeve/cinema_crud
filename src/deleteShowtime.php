<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

session_start();
// si l'utilisateur n'est pas connecté
if (!array_key_exists("user", $_SESSION)) {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

    // on assainie les variables
    $sanitizedEntries = filter_input_array(
        INPUT_POST,
        ['cinemaID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'heureDebut' => FILTER_DEFAULT,
        'heureFin' => FILTER_DEFAULT,
        'version' => FILTER_DEFAULT,
        'from' => FILTER_DEFAULT,
    ]
    );

    // suppression de la séance
    $seancesMgr->deleteShowtime(
        $sanitizedEntries['cinemaID'],
        $sanitizedEntries['filmID'],
        $sanitizedEntries['heureDebut'],
        $sanitizedEntries['heureFin']
    );
    // en fonction d'où je viens, je redirige
    if (strstr($sanitizedEntries['from'], 'movie')) {
        header('Location: movieShowtimes.php?filmID=' . $sanitizedEntries['filmID']);
        exit;
    } else {
        header('Location: cinemaShowtimes.php?cinemaID=' . $sanitizedEntries['cinemaID']);
        exit;
    }
} else {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}
