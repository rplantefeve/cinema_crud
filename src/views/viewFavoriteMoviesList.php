<?php $this->titre = "Films préférés"; ?>
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
    if ($preferences !== null) {
        // boucle de création du tableau
        foreach ($preferences as $prefere) {
            if ($addMode === "edit" && isset($toBeModified) === true && $prefere->getFilm()->getFilmId() === $toBeModified) {
                ?>
                <tr>
                    <form name="editFavoriteMovie" action="<?= $request->getBasePath() . '/favorite/save/' . $toBeModified ?>" method="POST">
                        <td><?= $preferenceToBeModified->getFilm()->getTitre() ?></td>
                        <td><textarea name="comment"><?= $preferenceToBeModified->getCommentaire() ?></textarea></td>
                        <td colspan="2" class="centered">
                            <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                            <input name="filmID" type="hidden" value="<?= $toBeModified ?>" />
                            <input name="modificationInProgress" type="hidden" value="" />
                            <input type="image" src="<?= $request->getBasePath() . '/images/cancelIcon.png' ?>" alt="Cancel" form="cancelForm" />
                            <input type="image" src="<?= $request->getBasePath() . '/images/validateIcon.png' ?>" alt="Validate" />
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
                        <form name="modifyFavoriteMovie" action="<?= $request->getBasePath() . '/favorite/list/edit/' . $prefere->getFilm()->getFilmId() ?>" method="GET">
                            <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                            <input type="image" src="<?= $request->getBasePath() . '/images/modifyIcon.png' ?>" alt="Modify" />
                        </form>
                    </td>
                    <td>
                        <form name="deleteFavoriteMovie" action="<?= $request->getBasePath() . '/favorite/delete/' . $utilisateur->getUserId() . '/' . $prefere->getFilm()->getFilmId() ?>" method="POST">
                            <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                            <input type="hidden" name="filmID" value="<?= $prefere->getFilm()->getFilmId() ?>" />
                            <input type="image" src="<?= $request->getBasePath() . '/images/deleteIcon.png' ?>" alt="Delete" />
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
            <form name="addFavoriteMovie" action="<?= $request->getBasePath() . '/favorite/add' ?>" method="POST">
                <td>
                    <select name="filmID">
                        <option value="">Choisissez un film</option>
                        <?php
                        // s'il y a des résultats
                        if ($films !== null) {
                            foreach ($films as $film) {
                                ?>
                                <option value="<?= $film->getFilmId(); ?>"><?= $film->getTitre(); ?></option>
                                <?php
                            }
                        } ?>
                    </select>
                    <?php
                    if (isset($noneSelected) === true && $noneSelected === true) : ?>
                        <div class="error">Veuillez renseigner un titre de film.</span>
                    <?php endif; ?>
                </td>
                <td>
                    <textarea name="comment" placeholder="Écrivez un commentaire ici..."></textarea>
                </td>
                <td colspan="2" class="centered">
                    <input type="hidden" name="userID" value="<?= $utilisateur->getUserId(); ?>" />
                    <input type="image" src="<?= $request->getBasePath() . '/images/cancelIcon.png' ?>" alt="Cancel" form="cancelForm" />
                    <input type="image" src="<?= $request->getBasePath() . '/images/addIcon.png' ?>" alt="Add" />
                </td>
            </form>
        </tr>
        <?php
    } else {
        ?>
        <tr class="new">
            <td colspan="4">
                <form name="addFavoriteMovie" method="get" action="<?= $request->getBasePath() . '/favorite/list/add' ?>">
                    <button class="add" type="submit">Cliquer pour ajouter un film préféré...</button>
                </form>
            </td>
        </tr>
        <?php
    } ?>
</table>
<form name="cancelForm" id="cancelForm" method="GET" action="<?= $request->getBasePath() . '/favorite/list' ?>">
</form>
<form name="backToMainPage" action="<?= $request->getBasePath() . '/home' ?>">
    <input type="submit" value="Retour à l'accueil"/>
</form>