<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

// variables de contrôles du formulaire de création
$isFirstNameEmpty = false;
$isLastNameEmpty = false;
$isEmailAddressEmpty = false;
$isUserUnique = true;
$isPasswordEmpty = false;
$isPasswordConfirmationEmpty = false;
$isPasswordValid = true;

// si la méthode POST est utilisée, cela signifie que le formulaire a été envoyé
if (filter_input(
    INPUT_SERVER,
    'REQUEST_METHOD'
) === "POST") {
    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(
        INPUT_POST,
        ['firstName' => FILTER_DEFAULT,
        'lastName' => FILTER_DEFAULT,
        'email' => FILTER_SANITIZE_EMAIL,
        'password' => FILTER_DEFAULT,
        'passwordConfirmation' => FILTER_DEFAULT]
    );

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
        $userID = $utilisateursMgr->getUserIDByEmailAddress($sanitizedEntries['email']);
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
        $password = password_hash(
            $sanitizedEntries['password'],
            PASSWORD_DEFAULT
        );
        // créer l'utilisateur
        $utilisateursMgr->createUser(
            $sanitizedEntries['firstName'],
            $sanitizedEntries['lastName'],
            $sanitizedEntries['email'],
            $password
        );

        session_start();
        // authentifier l'utilisateur
        $_SESSION['user'] = $sanitizedEntries['email'];
        $_SESSION['userID'] = $utilisateursMgr->getUserIDByEmailAddress($_SESSION['user']);
        // on redirige vers la page d'édition des films préférés
        header("Location: editFavoriteMoviesList.php");
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

// on inclut la vue correspondante
include __DIR__ . '/views/viewCreateUser.php';
