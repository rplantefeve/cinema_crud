<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/includes/fctManager.php';

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
        $userID = $fctManager->getUserIDByEmailAddress($sanitizedEntries['email']);
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
        $fctManager->createUser(
            $sanitizedEntries['firstName'],
                $sanitizedEntries['lastName'],
                $sanitizedEntries['email'],
                $password
        );

        session_start();
        // authentifier l'utilisateur
        $_SESSION['user'] = $sanitizedEntries['email'];
        $_SESSION['userID'] = $fctManager->getUserIDByEmailAddress($_SESSION['user']);
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
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Espace Personnel - Création</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header><h1>Création d'un nouvel utilisateur</h1></header>
        <form name="createUser" method="POST" action="createNewUser.php">
            <!-- la longueur maximum des input est en corrélation avec la longueur maximum des champs dans la BDD -->
            <label>Prénom :</label>
            <input name='firstName' type="text" maxlength="30" value="<?= $sanitizedEntries['firstName']
?>" />
            <div class="error">
                <?php
                if ($isFirstNameEmpty) {
                    echo "Veuillez renseigner un prénom.";
                }
                ?>
            </div>
            <label>Nom :</label>
            <input name='lastName' type="text" maxlength="50" value="<?= $sanitizedEntries['lastName'] ?>" />
            <div class="error">
                <?php
                if ($isLastNameEmpty) {
                    echo "Veuillez renseigner un nom.";
                }
                ?>
            </div>
            <label>Adresse email :</label>
            <input name='email' type="email" maxlength="90" value="<?= $sanitizedEntries['email'] ?>" />
            <div class="error">
                <?php
                if ($isEmailAddressEmpty) {
                    echo "Veuillez renseigner une adresse email.";
                } elseif (!$isUserUnique) {
                    echo "Cet utilisateur existe déjà !";
                }
                ?>
            </div>
            <label>Mot de passe :</label>
            <input name='password' type="password"/>
            <div class="error">
                <?php
                if ($isPasswordEmpty) {
                    echo "Veuillez rentrer un mot de passe.";
                }
                ?>
            </div>
            <label>Confirmation :</label>
            <input name='passwordConfirmation' type="password"/>
            <div class="error">
                <?php
                if ($isPasswordConfirmationEmpty) {
                    echo "Veuillez confirmer le mot de passe.";
                } elseif (!$isPasswordValid) {
                    echo "Les mots de passe ne correspondent pas !";
                }
                ?>
            </div>
            <input type="submit" value="Créer un nouvel utilisateur"/>
        </form>
        <form name="backToMainPage" action="index.php">
            <input type="submit" value="Retour à l'accueil"/>
        </form>
    </body>
</html>
