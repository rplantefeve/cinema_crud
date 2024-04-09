<?php $this->title = "Films préférés"; ?>
<header><h1><?= $utilisateur->getPrenom(); ?> <?= $utilisateur->getNom(); ?>, ci-dessous vos films préférés</h1></header>
<table class="std">
    <tr>
        <th>Titre</th>
        <th>Commentaire</th>
        <th colspan="2">Action</th>
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
    <tr class="new">
        <td colspan="4">
            <form name="addFavoriteMovie" method="get">
                <input name="action" type="hidden" value="editFavoriteMovie">
                <button class="add" type="submit">Cliquer pour ajouter un film préféré...</button>
            </form>
        </td>
    </tr>        
</table>

<form name="backToMainPage" action="index.php">
    <input type="submit" value="Retour à l'accueil"/>
</form>