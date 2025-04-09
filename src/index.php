<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/Manager.php';

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
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenue dans la gestion de cinéma</title>
        <link rel="stylesheet" href="css/cinema.css">
        <link rel="stylesheet" href="css/import/remixicon.css">
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
            <div class="mainbox">
                <div>
                    <h1>Accès espace personnel</h1>
                    <?php
                    // si pas encore authentifié
                    if (!$loginSuccess):
                        ?>
                        <form method="POST" name="editFavoriteMoviesList" action="index.php">
                            <label for="email">Adresse email : </label>
                            <input type="email" name="email" id="email" required>
                            <label for="password">Mot de passe  : </label>
                            <input type="password" name="password" id="password" required>
                            <div class="error">
                                <?php
                                if (!$areCredentialsOK):
                                    echo "Les informations de connexions ne sont pas correctes.";
                                endif;
                                ?>
                            </div>
                            <button type="submit" class="button-right">Editer ma liste de films préférés</button>
                        </form>
                        <p class="create-account">Pas encore d'espace personnel ? <a href="createNewUser.php">Commencer sa liste de films préférés.</a></p>
                        <?php
                    // sinon (utilisateur authentifié)
                    else:
                        ?>
                        <p>Vous êtes connecté en tant que <?= $_SESSION['user'] ?>.</p>
                        <form action="editFavoriteMoviesList.php">
                            <button type="submit" class="button-right"><i class="ri-film-line"></i>&nbsp;Editer ma liste de films préférés</button>
                        </form>
                        <a href="logout.php">Se déconnecter</a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Gestion des cinémas -->
            <div class="mainbox">
                <div>
                    <h1>Gestion des cinémas</h1>
                    <div class="button-container">
                        <form name="cinemasList" action="cinemasList.php">
                            <button type="submit">Consulter la liste des cinémas</button>
                        </form>
                        <form name="moviesList" action="moviesList.php">
                            <button type="submit">Consulter la liste des films</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <span class="copyleft">&copy;</span> 2025 Gestion de Cinéma. Tous droits inversés.
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
