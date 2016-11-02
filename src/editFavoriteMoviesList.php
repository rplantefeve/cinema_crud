<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . './includes/Manager.php';

// TODO
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Espace personnel - Films préférés</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header><h1>Prénom NOM, ci-dessous vos films préférés</h1></header>
        <table class="std">
            <tr>
                <th>Titre</th>
                <th>Commentaire</th>
            </tr>
            <?php
            // TODO
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
