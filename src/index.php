<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/includes/fctManager.php';

// initialisation de l'application
require_once __DIR__ . '/init.php';

session_start();
// personne d'authentifié à ce niveau
$loginSuccess = false;

// variables de contrôle du formulaire
$areCredentialsOK = true;

// si l'utilisateur est déjà authentifié
if (array_key_exists("user", $_SESSION)) {
    $loginSuccess = true;
    // Sinon (pas d'utilisateur authentifié pour l'instant)
} else {
    // si la méthode POST a été employée
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {
        // on "sainifie" les entrées
        $sanitizedEntries = filter_input_array(
            INPUT_POST,
                ['email' => FILTER_SANITIZE_EMAIL,
                'password' => FILTER_DEFAULT]
        );
        try {
            // On vérifie l'existence de l'utilisateur
            $fctManager->verifyUserCredentials(
                $sanitizedEntries['email'],
                $sanitizedEntries['password']
            );

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
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenue dans la gestion de cinéma</title>
        <link rel="stylesheet" href="css/cinema.css">
        <script src="js/main.js"></script>
    </head>
    <body>
        <header>
            <h1>Bienvenue dans l'application de gestion de cinéma</h1>
        </header>
        <main>
            <p>Gérez votre cinéma efficacement avec notre application. Vous pouvez ajouter, mettre à jour et supprimer des enregistrements de films, gérer les horaires des séances, et bien plus encore.</p>
            <div id="error" class="error"></div>
            <div id="info" class="info"></div>
        <?php
        // si pas encore authentifié
        if (!$loginSuccess):
            ?>
            <form method="POST" name="editFavoriteMoviesList" action="index.php">
                <label>Adresse email : </label>
                <input type="email" name="email" required>
                <label>Mot de passe  : </label>
                <input type="password" name="password" required>
                <div class="error">
                    <?php
                    if (!$areCredentialsOK):
                        echo "Les informations de connexions ne sont pas correctes.";
                    endif;
                    ?>
                </div>
                <button type="submit">Editer ma liste de films préférés</button>
            </form>
            <p>Pas encore d'espace personnel ? <a href="createNewUser.php">Créer sa liste de films préférés.</a></p>
            <?php
        // sinon (utilisateur authentifié)
        else:
            ?>
            <p>Vous êtes connecté en tant que <?= $_SESSION['user'] ?>.</p>
            <form action="editFavoriteMoviesList.php">
                <button type="submit">Editer ma liste de films préférés</button>
            </form>
            <a href="logout.php">Se déconnecter</a>
        <?php endif; ?>
        </main>
        <footer>
            <p><span class="copyleft">&copy;</span> 2025 Gestion de Cinéma. Tous droits inversés.</p>
        </footer>
        <script>
            <?php if (isset($errorMessage)): ?>
                showMessage('error', '<?= $errorMessage ?>');
            <?php endif; ?>
            <?php if (isset($infoMessage)): ?>
                showMessage('info', '<?= $infoMessage ?>');
            <?php endif; ?>
        </script>
    </body>
</html>
