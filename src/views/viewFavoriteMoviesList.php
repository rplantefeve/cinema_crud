<?php $this->title = "Films préférés"; ?>
<header>
    <h1><?= $utilisateur->getPrenom(); ?> <?= $utilisateur->getNom(); ?>, ci-dessous vos films préférés</h1>
</header>
<table class="std">
    <tr>
        <th>Titre</th>
        <th>Commentaire</th>
        <th colspan="2">Action</th>
    </tr>
    <?php
    // si des films ont été trouvés
    if ($preferences) {
        // boucle de création du tableau
        foreach ($preferences as $prefere) {
            if ($addMode === "edit" && isset($toBeModified) && $prefere->getFilm()->getFilmId() === $toBeModified) {
            ?>
                <tr>
                    <form name="editFavoriteMovie" action="index.php?action=saveFavoriteMovie" method="POST">
                        <td><?= $preferenceToBeModified->getFilm()->getTitre() ?></td>
                        <td><textarea name="comment"><?= $preferenceToBeModified->getCommentaire() ?></textarea></td>
                        <td colspan="2" class="centered">
                            <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                            <input name="filmID" type="hidden" value="<?= $preferenceToBeModified->getFilm()->getFilmId() ?>" />
                            <input name="modificationInProgress" type="hidden" value="" />
                            <input type="image" src="images/cancelIcon.png" alt="Cancel" form="cancelForm" />
                            <input type="image" src="images/validateIcon.png" alt="Add" />
                        </td>
                    </form>
                </tr>
            <?php
            } else {
            ?>
                <tr>
                    <td><?= $prefere->getFilm()->getTitre(); ?></td>
                    <td><?= $prefere->getCommentaire(); ?></td>
                    <td>
                        <form name="modifyFavoriteMovie" action="index.php" method="GET">
                            <input type="hidden" name="action" value="editFavoriteMovie" />
                            <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                            <input type="hidden" name="filmID" value="<?= $prefere->getFilm()->getFilmId(); ?>" />
                            <input type="image" src="images/modifyIcon.png" alt="Modify" />
                        </form>
                    </td>
                    <td>
                        <form name="deleteFavoriteMovie" action="index.php?action=deleteFavoriteMovie" method="POST">
                            <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                            <input type="hidden" name="filmID" value="<?= $prefere->getFilm()->getFilmId() ?>" />
                            <input type="image" src="images/deleteIcon.png" alt="Delete" />
                        </form>
                    </td>
                </tr>
            <?php
            }
        }
    }
    if ($addMode === "add") {
        ?>
        <tr>
            <form name="addFavoriteMovie" action="index.php?action=saveFavoriteMovie" method="POST">
                <td>
                    <select name="filmID">
                        <option value="default">Choisissez un film</option>
                        <?php
                        // s'il y a des résultats
                        if ($films) {
                            foreach ($films as $film) {
                        ?>
                                <option value="<?= $film->getFilmId(); ?>"><?= $film->getTitre(); ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                    <?php
                    if (isset($noneSelected) && $noneSelected === true) : ?>
                        <div class="error">Veuillez renseigner un titre de film.</span>
                        <?php endif; ?>
                </td>
                <td>
                    <textarea name="comment" placeholder="Écrivez un commentaire ici..."></textarea>
                </td>
                <td colspan="2" class="centered">
                    <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                    <input type="image" src="images/cancelIcon.png" alt="Cancel" form="cancelForm" />
                    <input type="image" src="images/addIcon.png" alt="Add" />
                </td>
            </form>
        </tr>
    <?php
    } else {
    ?>
        <tr class="new">
            <td colspan="4">
                <form name="addFavoriteMovie" method="get">
                    <input name="action" type="hidden" value="addFavoriteMovie">
                    <button class="add" type="submit">Cliquer pour ajouter un film préféré...</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
<form name="cancelForm" id="cancelForm" method="GET" action="index.php">
    <input name="action" type="hidden" value="editFavoriteMoviesList" />
</form>
<form name="backToMainPage" action="index.php">
    <input type="submit" value="Retour à l'accueil" />
</form>