<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . './includes/fctManager.php';

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
    $utilisateur = $fctManager->getCompleteUsernameByEmailAddress($_SESSION['user']);
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
        <title>Espace personnel - Films préférés</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header><h1><?= $utilisateur['prenom'] ?> <?= $utilisateur['nom'] ?>, ci-dessous vos films préférés</h1></header>
        <table class="std">
            <tr>
                <th>Titre</th>
                <th>Commentaire</th>
            </tr>
            <?php
            // on récupère la liste des films préférés grâce à l'utilisateur identifié
            $films = $fctManager->getFavoriteMoviesFromUser($utilisateur['userID']);
            // si des films ont été trouvés
            if ($films) {
                // boucle de création du tableau
                foreach ($films as $film) {
                    ?>
                    <tr>
                        <td><?= $film['titre'] ?></td>
                        <td><?= $film['commentaire'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <form name="addFavoriteMovie" action="editFavoriteMovie.php">
            <input type="submit" value="Ajouter un film préféré"/>
        </form>
        <form name="backToMainPage" action="index.php">
            <input type="submit" value="Retour à l'accueil"/>
        </form>
    </body>
</html>
