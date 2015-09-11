<?php $this->titre = "Films préférés"; ?>
<header><h1><?= $utilisateur->getPrenom(); ?> <?= $utilisateur->getNom(); ?>, ci-dessous vos films préférés</h1></header>
<table class="std">
    <tr>
        <th>Titre</th>
        <th>Commentaire</th>
    </tr>
    <?php
    // si des films ont été trouvés
    if ($preferes) {
        // boucle de création du tableau
        foreach ($preferes as $prefere) {
            ?>
            <tr>
                <td><?= $prefere->getFilm()->getTitre(); ?></td>
                <td><?= $prefere->getCommentaire(); ?></td>
                <td>
                    <form name="modifyFavoriteMovie" action="index.php" method="GET">
                        <input type="hidden" name="action" value="editFavoriteMovie"/>
                        <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>"/>
                        <input type="hidden" name="filmID" value="<?= $prefere->getFilm()->getFilmId(); ?>"/>
                        <input type="image" src="images/modifyIcon.png" alt="Modify"/>
                    </form>
                </td>
                <td>
                    <form name="deleteFavoriteMovie" action="index.php?action=deleteFavoriteMovie" method="POST">
                        <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>"/>
                        <input type="hidden" name="filmID" value="<?= $prefere->getFilm()->getFilmId() ?>"/>
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