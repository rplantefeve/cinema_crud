<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . './includes/fctManager.php';

// initialisation de l'application
require_once __DIR__ . './init.php';

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
            $fctManager->verifyUserCredentials($sanitizedEntries['email'],
                    $sanitizedEntries['password']);

            // on enregistre l'utilisateur
            $_SESSION['user'] = $sanitizedEntries['email'];
            $_SESSION['userID'] = $fctManager->getUserIDByEmailAddress($_SESSION['user']);
            // on redirige vers la page d'édition des films préférés
            header("Location: editFavoriteMoviesList.php");
            exit;
        } catch (Exception $ex) {
            $areCredentialsOK = false;
            $logger->error($ex->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cinéma CRUD</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <div>
            <header>
                <h1>Espace personnel</h1>
            </header>
            <?php
            // si pas encore authentifié
            if (!$loginSuccess):
                ?>
                <form method="POST" name="editFavoriteMoviesList" action="index.php">

                    <label>Adresse email : </label>
                    <input type="email" name="email" required/>
                    <label>Mot de passe  : </label>
                    <input type="password" name="password" required/>
                    <div class="error">
                        <?php
                        if (!$areCredentialsOK):
                            echo "Les informations de connexions ne sont pas correctes.";
                        endif;
                        ?>
                    </div>
                    <input type="submit" value="Editer ma liste de films préférés"/>
                </form>
                <p>Pas encore d'espace personnel ? <a href="createNewUser.php">Créer sa liste de films préférés.</a></p>
                <?php
            // sinon (utilisateur authentifié)
            else:
                ?>
                <form action="editFavoriteMoviesList.php">
                    <input type="submit" value="Editer ma liste de films préférés"/>
                </form>
                <a href="logout.php">Se déconnecter</a>
            <?php endif; ?>
        </div>
    </body>
</html>
