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

// init. des flags. Etat par défaut => je viens du cinéma et je créé
$fromCinema = true;
$fromFilm = false;
$isItACreation = true;

// init. des variables du formulaire
$seance = ['dateDebut' => '',
    'heureDebut' => '',
    'dateFin' => '',
    'heureFin' => '',
    'dateheureDebutOld' => '',
    'dateheureFinOld' => '',
    'heureFinOld' => '',
    'version' => ''];

// si l'on est en GET
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET') {
    // on assainie les variables
    $sanitizedEntries = filter_input_array(
        INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'from' => FILTER_SANITIZE_STRING,
        'heureDebut' => FILTER_SANITIZE_STRING,
        'heureFin' => FILTER_SANITIZE_STRING,
        'version' => FILTER_SANITIZE_STRING]
    );
    // pour l'instant, on vérifie les données en GET
    if ($sanitizedEntries && isset($sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'], $sanitizedEntries['from'])) {
        // on récupère l'identifiant du cinéma
        $cinemaID = $sanitizedEntries['cinemaID'];
        // l'identifiant du film
        $filmID = $sanitizedEntries['filmID'];
        // d'où vient on ?
        $from = $sanitizedEntries['from'];
        // puis on récupère les informations du cinéma en question
        $cinema = $cinemasMgr->getCinemaInformationsByID($cinemaID);
        // puis on récupère les informations du film en question
        $film = $filmsMgr->getMovieInformationsByID($filmID);

        // s'il on vient des séances du film
        if (strstr($sanitizedEntries['from'], 'movie')) {
            $fromCinema = false;
            // on vient du film
            $fromFilm = true;
        }

        // ici, on veut savoir si on modifie ou si on ajoute
        if (isset($sanitizedEntries['heureDebut'],
                        $sanitizedEntries['heureFin'],
                        $sanitizedEntries['version'])) {
            // nous sommes dans le cas d'une modification
            $isItACreation = false;
            // on récupère les anciennes valeurs (utile pour retrouver la séance avant de la modifier
            $seance['dateheureDebutOld'] = $sanitizedEntries['heureDebut'];
            $seance['dateheureFinOld'] = $sanitizedEntries['heureFin'];
            // dates PHP
            $dateheureDebut = new DateTime($sanitizedEntries['heureDebut']);
            $dateheureFin = new DateTime($sanitizedEntries['heureFin']);
            // découpage en heures
            $seance['heureDebut'] = $dateheureDebut->format("H:i");
            $seance['heureFin'] = $dateheureFin->format("H:i");
            // découpage en jour/mois/année
            $seance['dateDebut'] = $dateheureDebut->format("d/m/Y");
            $seance['dateFin'] = $dateheureFin->format("d/m/Y");
            // on récupère la version
            $seance['version'] = $sanitizedEntries['version'];
        }
    }
    // sinon, on retourne à l'accueil
    else {
        header('Location: index.php');
        exit();
    }
    // sinon, on est en POST
} elseif (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
    // on assainie les variables
    $sanitizedEntries = filter_input_array(
        INPUT_POST,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'datedebut' => FILTER_SANITIZE_STRING,
        'heuredebut' => FILTER_SANITIZE_STRING,
        'datefin' => FILTER_SANITIZE_STRING,
        'heurefin' => FILTER_SANITIZE_STRING,
        'dateheurefinOld' => FILTER_SANITIZE_STRING,
        'dateheuredebutOld' => FILTER_SANITIZE_STRING,
        'version' => FILTER_SANITIZE_STRING,
        'from' => FILTER_SANITIZE_STRING,
        'modificationInProgress' => FILTER_SANITIZE_STRING]
    );
    // si toutes les valeurs sont renseignées
    if ($sanitizedEntries && isset($sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'], $sanitizedEntries['datedebut'],
                    $sanitizedEntries['heuredebut'],
                    $sanitizedEntries['datefin'], $sanitizedEntries['heurefin'],
                    $sanitizedEntries['dateheuredebutOld'],
                    $sanitizedEntries['dateheurefinOld'],
                    $sanitizedEntries['version'], $sanitizedEntries['from'])) {
        // nous sommes en Français
        setlocale(LC_TIME, 'fra_fra');
        // date du jour de projection de la séance
        $datetimeDebut = new DateTime($sanitizedEntries['datedebut'] . ' ' . $sanitizedEntries['heuredebut']);
        $datetimeFin = new DateTime($sanitizedEntries['datefin'] . ' ' . $sanitizedEntries['heurefin']);
        // Est-on dans le cas d'une insertion ?
        if (!isset($sanitizedEntries['modificationInProgress'])) {
            // j'insère dans la base
            $resultat = $seancesMgr->insertNewShowtime(
                $sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'],
                    $datetimeDebut->format("Y-m-d H:i"),
                    $datetimeFin->format("Y-m-d H:i"),
                    $sanitizedEntries['version']
            );
        } else {
            // c'est une mise à jour
            $resultat = $seancesMgr->updateShowtime(
                $sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'],
                    $sanitizedEntries['dateheuredebutOld'],
                    $sanitizedEntries['dateheurefinOld'],
                    $datetimeDebut->format("Y-m-d H:i"),
                    $datetimeFin->format("Y-m-d H:i"),
                    $sanitizedEntries['version']
            );
        }
        // en fonction d'où je viens, je redirige
        if (strstr($sanitizedEntries['from'], 'movie')) {
            header('Location: movieShowtimes.php?filmID=' . $sanitizedEntries['filmID']);
            exit;
        } else {
            header('Location: cinemaShowtimes.php?cinemaID=' . $sanitizedEntries['cinemaID']);
            exit;
        }
    }
}
// sinon, on retourne à l'accueil
else {
    header('Location: index.php');
    exit();
}

// On appelle la vue
include __DIR__ . '/views/viewEditShowtime.php';
