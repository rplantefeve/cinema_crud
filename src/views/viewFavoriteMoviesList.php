<?php $this->titre = "Films préférés"; ?>
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
                    <form name="modifyFavoriteMovie" action="<?= $request->getBasePath() . '/favorite/edit/' . $utilisateur->getUserId() . '/' . $prefere->getFilm()->getFilmId() ?>" method="get">
                        <input type="submit" id="modify" value=""/>
                    </form>
                </td>
                <td>
                    <form name="deleteFavoriteMovie" action="<?= $request->getBasePath() . '/favorite/delete/' . $utilisateur->getUserId() . '/' . $prefere->getFilm()->getFilmId() ?>" method="POST">
                        <input type="image" src="<?= $request->getBasePath() . '/images/deleteIcon.png' ?>" alt="Delete"/>
                    </form>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    <tr class="new">
        <td colspan="4">
            <form name="addFavoriteMovie" method="get" action="<?= $request->getBasePath() . '/favorite/add' ?>">
                <button class="add" type="submit">Cliquer pour ajouter un film préféré...</button>
            </form>
        </td>
    </tr>        
</table>

<form name="backToMainPage" action="<?= $request->getBasePath() . '/home' ?>">
    <input type="submit" value="Retour à l'accueil"/>
</form>