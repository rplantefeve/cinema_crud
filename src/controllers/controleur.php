<?php

use Semeformation\Mvc\Cinema_crud\views\View;

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
                // redirection vers la liste des préférences de films
                header("Location: index.php?action=editFavoriteMoviesList");
                exit;
            } catch (Exception $ex) {
                $areCredentialsOK = false;
                $managers['utilisateursMgr']->getLogger()->error($ex->getMessage());
            }
        }
    }

    // On génère la vue Accueil
    $vue = new View("Home");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer([
        'areCredentialsOK' => $areCredentialsOK,
        'loginSuccess' => $loginSuccess]);
}

/*
 * Route liste des cinémas
 */

function cinemasList($managers) {
    // on récupère la liste des cinémas ainsi que leurs informations
    $cinemas = $managers['cinemasMgr']->getCinemasList();

    // On génère la vue films
    $vue = new View("CinemasList");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer(['cinemas' => $cinemas]);
}

function moviesList($managers) {
    // on récupère la liste des films ainsi que leurs informations
    $films = $managers['filmsMgr']->getMoviesList();

    // On génère la vue films
    $vue = new View("MoviesList");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer(['films' => $films]);
}

function movieShowtimes($managers) {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT]);
    // si l'identifiant du film a bien été passé en GET'
    if ($sanitizedEntries && !is_null($sanitizedEntries['filmID']) && $sanitizedEntries['filmID'] !== '') {
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
    $seances = $managers['seancesMgr']->getAllCinemasShowtimesByMovieID($cinemas,
            $filmID);

    // On génère la vue séances du film
    $vue = new View("MovieShowtimes");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer([
        'cinemas' => $cinemas,
        'film' => $film,
        'seances' => $seances]);
}

function cinemaShowtimes($managers) {
    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

    // si l'identifiant du cinéma a bien été passé en GET
    if ($sanitizedEntries && !is_null($sanitizedEntries) && $sanitizedEntries['cinemaID'] != '') {
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
    // on récupère toutes les séances de films pour un cinéma donné
    $seances = $managers['seancesMgr']->getAllMoviesShowtimesByCinemaID($films,
            $cinemaID);

    // On génère la vue séances du cinéma
    $vue = new View("CinemaShowtimes");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer([
        'cinema' => $cinema,
        'films' => $films,
        'seances' => $seances]);
}

function editFavoriteMoviesList($managers) {
    session_start();
    // si l'utilisateur n'est pas connecté
    if (!array_key_exists("user",
                    $_SESSION)) {
        // renvoi à la page d'accueil
        header('Location: index.php');
        exit;
    }
    // l'utilisateur est loggué
    else {
        $utilisateur = $managers['utilisateursMgr']->getCompleteUsernameByEmailAddress($_SESSION['user']);
    }

    // on récupère la liste des films préférés grâce à l'utilisateur identifié
    $films = $managers['preferesMgr']->getFavoriteMoviesFromUser($utilisateur['userID']);

    // On génère la vue Films préférés
    $vue = new View("FavoriteMoviesList");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer(array(
        'utilisateur' => $utilisateur,
        'films' => $films));
}

function editFavoriteMovie($managers) {
    session_start();
    // si l'utilisateur n'est pas connecté
    if (!array_key_exists("user",
                    $_SESSION)) {
        // renvoi à la page d'accueil
        header('Location: index.php');
        exit;
    }

    $films = null;
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
        if (!is_null($sanitizedEntries['backToList'])) {
            // redirection vers la liste des préférences de films
            header("Location: index.php?action=editFavoriteMoviesList");
            exit;
        }
        // sinon (l'action demandée est la sauvegarde d'un favori)
        else {
            // si un film a été selectionné 
            if (!is_null($sanitizedEntries['filmID'])) {

                // et que nous ne sommes pas en train de modifier une préférence
                if (is_null($sanitizedEntries['modificationInProgress'])) {
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
                // redirection vers la liste des préférences de films
                header("Location: index.php?action=editFavoriteMoviesList");
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

        if ($sanitizedEntries && !is_null($sanitizedEntries['filmID']) && $sanitizedEntries['filmID'] !== '' && !is_null($sanitizedEntries['userID']) && $sanitizedEntries['userID'] !== '') {
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

    $donnees = ['aFilmIsSelected' => $aFilmIsSelected,
        'isItACreation' => $isItACreation,
        'preference' => $preference,
        'userID' => $preference['userID'],
        'films' => $films
    ];
    // On génère la vue Films préférés
    $vue = new View("FavoriteMovie");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer($donnees);
}

function deleteFavoriteMovie($managers) {
    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_POST,
            ['userID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT]);

    // suppression de la préférence de film
    $managers['preferesMgr']->deleteFavoriteMovie($sanitizedEntries['userID'],
            $sanitizedEntries['filmID']);
    // redirection vers la liste des préférences de films
    header("Location: index.php?action=editFavoriteMoviesList");
    exit;
}

function createNewUser($managers) {
    // variables de contrôles du formulaire de création
    $isFirstNameEmpty = false;
    $isLastNameEmpty = false;
    $isEmailAddressEmpty = false;
    $isUserUnique = true;
    $isPasswordEmpty = false;
    $isPasswordConfirmationEmpty = false;
    $isPasswordValid = true;

    // si la méthode POST est utilisée, cela signifie que le formulaire a été envoyé
    if (filter_input(INPUT_SERVER,
                    'REQUEST_METHOD') === "POST") {
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(INPUT_POST,
                ['firstName' => FILTER_SANITIZE_STRING,
            'lastName' => FILTER_SANITIZE_STRING,
            'email' => FILTER_SANITIZE_EMAIL,
            'password' => FILTER_DEFAULT,
            'passwordConfirmation' => FILTER_DEFAULT]);

        // si le prénom n'a pas été renseigné
        if ($sanitizedEntries['firstName'] === "") {
            $isFirstNameEmpty = true;
        }

        // si le nom n'a pas été renseigné
        if ($sanitizedEntries['lastName'] === "") {
            $isLastNameEmpty = true;
        }

        // si l'adresse email n'a pas été renseignée
        if ($sanitizedEntries['email'] === "") {
            $isEmailAddressEmpty = true;
        } else {
            // On vérifie l'existence de l'utilisateur
            $userID = $managers['utilisateursMgr']->getUserIDByEmailAddress($sanitizedEntries['email']);
            // si on a un résultat, cela signifie que cette adresse email existe déjà
            if ($userID) {
                $isUserUnique = false;
            }
        }
        // si le password n'a pas été renseigné
        if ($sanitizedEntries['password'] === "") {
            $isPasswordEmpty = true;
        }
        // si la confirmation du password n'a pas été renseigné
        if ($sanitizedEntries['passwordConfirmation'] === "") {
            $isPasswordConfirmationEmpty = true;
        }

        // si le mot de passe et sa confirmation sont différents
        if ($sanitizedEntries['password'] !== $sanitizedEntries['passwordConfirmation']) {
            $isPasswordValid = false;
        }

        // si les champs nécessaires ne sont pas vides, que l'utilisateur est unique et que le mot de passe est valide
        if (!$isFirstNameEmpty && !$isLastNameEmpty && !$isEmailAddressEmpty && $isUserUnique && !$isPasswordEmpty && $isPasswordValid) {
            // hash du mot de passe
            $password = password_hash($sanitizedEntries['password'],
                    PASSWORD_DEFAULT);
            // créer l'utilisateur
            $managers['utilisateursMgr']->createUser($sanitizedEntries['firstName'],
                    $sanitizedEntries['lastName'],
                    $sanitizedEntries['email'],
                    $password);

            session_start();
            // authentifier l'utilisateur
            $_SESSION['user'] = $sanitizedEntries['email'];
            $_SESSION['userID'] = $managers['utilisateursMgr']->getUserIDByEmailAddress($_SESSION['user']);
            // redirection vers la liste des préférences de films
            header("Location: index.php?action=editFavoriteMoviesList");
            exit;
        }
    }
    // sinon (le formulaire n'a pas été envoyé)
    else {
        // initialisation des variables du formulaire
        $sanitizedEntries['firstName'] = '';
        $sanitizedEntries['lastName'] = '';
        $sanitizedEntries['email'] = '';
    }

    $donnees = [
        'sanitizedEntries' => $sanitizedEntries,
        'isFirstNameEmpty' => $isFirstNameEmpty,
        'isLastNameEmpty' => $isLastNameEmpty,
        'isEmailAddressEmpty' => $isEmailAddressEmpty,
        'isUserUnique' => $isUserUnique,
        'isPasswordEmpty' => $isPasswordEmpty,
        'isPasswordConfirmationEmpty' => $isPasswordConfirmationEmpty,
        'isPasswordValid' => $isPasswordValid];
    // On génère la vue Création d'un utilisateur
    $vue = new View("CreateUser");
    // En passant les variables nécessaires à son bon affichage
    $vue->generer($donnees);
}

function logout() {
    session_start();
    session_destroy();
    header('Location: index.php');
}

function error($e) {

    $vue = new View("Error");
    $vue->generer(['messageErreur' => $e->getMessage()]);
}
