<?php

/*
 * Route Accueil
 */

function home($managers) {
    session_start();
    // personne d'authentifié à ce niveau
    $loginSuccess = false;

    // variables de contrôle du formulaire
    $areCredentialsOK = true;

    // si l'utilisateur est déjà authentifié
    if (array_key_exists("user",
                    $_SESSION)) {
        $loginSuccess = true;
        // Sinon (pas d'utilisateur authentifié pour l'instant)
    } else {
        // si la méthode POST a été employée
        if (filter_input(INPUT_SERVER,
                        'REQUEST_METHOD') === "POST") {
            // on "sainifie" les entrées
            $sanitizedEntries = filter_input_array(INPUT_POST,
                    ['email' => FILTER_SANITIZE_EMAIL,
                'password' => FILTER_DEFAULT]);
            try {
                // On vérifie l'existence de l'utilisateur
                $managers['utilisateursMgr']->verifyUserCredentials($sanitizedEntries['email'],
                        $sanitizedEntries['password']);

                // on enregistre l'utilisateur
                $_SESSION['user'] = $sanitizedEntries['email'];
                $_SESSION['userID'] = $managers['utilisateursMgr']->getUserIDByEmailAddress($_SESSION['user']);
                // on redirige vers la page d'édition des films préférés
                header("Location: editFavoriteMoviesList.php");
                exit;
            } catch (Exception $ex) {
                $areCredentialsOK = false;
                $managers['utilisateursMgr']->getLogger()->error($ex->getMessage());
            }
        }
    }

    // On inclut la vue principale
    include dirname(__DIR__) . './views/viewHome.php';
}

/*
 * Route liste des cinémas
 */

function cinemasList($managers) {
    // on récupère la liste des cinémas ainsi que leurs informations
    $cinemas = $managers['cinemasMgr']->getCinemasList();

    // on inclut la vue correspondante
    include dirname(__DIR__) . './views/viewCinemasList.php';
}

function moviesList($managers) {
    // on récupère la liste des films ainsi que leurs informations
    $films = $managers['filmsMgr']->getMoviesList();

    // on inclut la vue correspondante
    include dirname(__DIR__) . './views/viewMoviesList.php';
}

function movieShowtimes($managers) {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT]);
    // si l'identifiant du film a bien été passé en GET'
    if ($sanitizedEntries && $sanitizedEntries['filmID'] !== NULL && $sanitizedEntries['filmID'] !== '') {
        // on récupère l'identifiant du cinéma
        $filmID = $sanitizedEntries['filmID'];
        // puis on récupère les informations du film en question
        $film = $managers['filmsMgr']->getMovieInformationsByID($filmID);
    }
    // sinon, on retourne à l'accueil
    else {
        header('Location: index.php');
        exit();
    }

    // on récupère la liste des cinémas de ce film
    $cinemas = $managers['cinemasMgr']->getMovieCinemasByMovieID($filmID);

    // on inclut la vue correspondante
    include dirname(__DIR__) . './views/viewMovieShowtimes.php';
}

function cinemaShowtimes($managers) {
    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

    // si l'identifiant du cinéma a bien été passé en GET
    if ($sanitizedEntries && $sanitizedEntries['cinemaID'] !== NULL && $sanitizedEntries['cinemaID'] != '') {
        // on récupère l'identifiant du cinéma
        $cinemaID = $sanitizedEntries['cinemaID'];
        // puis on récupère les informations du cinéma en question
        $cinema = $managers['cinemasMgr']->getCinemaInformationsByID($cinemaID);
    }
    // sinon, on retourne à l'accueil
    else {
        header('Location: index.php');
        exit();
    }

    // on récupère la liste des films de ce cinéma
    $films = $managers['filmsMgr']->getCinemaMoviesByCinemaID($cinemaID);

    // On appelle la vue
    include dirname(__DIR__) . './views/viewCinemaShowtimes.php';
}

function logout() {
    session_start();
    session_destroy();
    header('Location: index.php');
}

function error($e) {
    $messageErreur = $e->getMessage();

    include dirname(__DIR__) . './views/viewError.php';
}
