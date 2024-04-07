<!DOCTYPE html>
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
                <th colspan="2">Action</th>
            </tr>
            <?php
            // si des films ont été trouvés
            if ($films !== null) {
                // boucle de création du tableau
                foreach ($films as $film) {
                    ?>
                    <tr>
                        <td><?= $film['titre'] ?></td>
                        <td><?= $film['commentaire'] ?></td>
                        <td>
                            <form name="modifyFavoriteMovie" action="editFavoriteMovie.php" method="GET">
                                <input type="hidden" name="userID" value="<?= $utilisateur['userID'] ?>"/>
                                <input type="hidden" name="filmID" value="<?= $film['filmID'] ?>"/>
                                <input type="image" src="images/modifyIcon.png" alt="Modify"/>
                            </form>
                        </td>
                        <td>
                            <form name="deleteFavoriteMovie" action="deleteFavoriteMovie.php" method="POST">
                                <input type="hidden" name="userID" value="<?= $utilisateur['userID'] ?>"/>
                                <input type="hidden" name="filmID" value="<?= $film['filmID'] ?>"/>
                                <input type="image" src="images/deleteIcon.png" alt="Delete"/>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr class="new">
                <td colspan="4">
                    <form name="addFavoriteMovie" action="editFavoriteMovie.php">
                        <button class="add" type="submit">Cliquer pour ajouter un film préféré...</button>
                    </form>
                </td>
            </tr>        
        </table>

        <form name="backToMainPage" action="index.php">
            <input type="submit" value="Retour à l'accueil"/>
        </form>
    </body>
</html>
