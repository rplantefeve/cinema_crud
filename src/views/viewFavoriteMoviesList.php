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
            </tr>
            <?php
            // si des films ont été trouvés
            if ($films) {
                // boucle de création du tableau
                foreach ($films as $film) {
                    ?>
                    <tr>
                        <td><?= $film['titre'] ?></td>
                        <td><?= $film['commentaire'] ?></td>
                        <td>
                            <form name="modifyFavoriteMovie" action="index.php" method="GET">
                                <input type="hidden" name="action" value="editFavoriteMovie"/>
                                <input type="hidden" name="userID" value="<?= $utilisateur['userID'] ?>"/>
                                <input type="hidden" name="filmID" value="<?= $film['filmID'] ?>"/>
                                <input type="image" src="images/modifyIcon.png" alt="Modify"/>
                            </form>
                        </td>
                        <td>
                            <form name="deleteFavoriteMovie" action="index.php?action=deleteFavoriteMovie" method="POST">
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
        </table>
        <form name="addFavoriteMovie" action="index.php">
            <input type="hidden" name="action" value="editFavoriteMovie"/>
            <input type="submit" value="Ajouter un film préféré"/>
        </form>
        <form name="backToMainPage" action="index.php">
            <input type="submit" value="Retour à l'accueil"/>
        </form>
    </body>
</html>
