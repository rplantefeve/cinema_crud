<?php
$this->titre = "Editer un film préféré";
$action      = $request->getBasePath() . '/favorite/edit/' . $prefere->getUtilisateur()->getUserId();
// si c'est une modification
if ($prefere->getFilm()) {
    $action .= '/' . $prefere->getFilm()->getFilmId();
}
?>
<header><h1>Ajout / Modification d'un film préféré</h1></header>
<form method="POST" name="editFavoriteMovie" action="<?= $action ?>">
    <label>Titre :</label>
    <select name="filmID" <?php
    if ($prefere->getFilm()): echo "disabled";
    endif;
    ?>>
                <?php
                // si c'est une création, on crée la liste des films dynamiquement
                if (is_null($prefere->getFilm())) {
                    // s'il y a des résultats
                    if ($films) {
                        foreach ($films as $film) {
                            ?>
                    <option value="<?= $film->getFilmId(); ?>"><?= $film->getTitre(); ?></option>
                    <?php
                }
            }
        }
        // sinon, c'est une modification, nous n'avons qu'une seule option dans la liste
        else {
            ?>
            <option selected="selected" value="<?= $prefere->getFilm()->getFilmId() ?>"><?= $prefere->getFilm()->getTitre() ?></option>
            <?php
        }
        ?>
    </select>
    <div class="error">
        <?php
        if (!$aFilmIsSelected) {
            echo "Veuillez renseigner un titre de film.";
        }
        ?>
    </div>
    <label>Commentaire :</label>
    <textarea name="comment"><?= $prefere->getCommentaire() ?></textarea>
    <br/>
    <?php
    // si c'est une modification, c'est une information dont nous avons besoin
    if ($prefere->getFilm()) {
        ?>
        <input type="hidden" name="filmID" value="<?= $prefere->getFilm()->getFilmId() ?>">
        <?php
    }
    ?>
    <input type="hidden" value="<?= $prefere->getUtilisateur()->getUserId() ?>" name="userID"/>
    <input type="submit" name="saveEntry" value="Sauvegarder" <?php
    if (is_null($prefere->getFilm())) {
        echo ' formaction = "' . $request->getBasePath() . '/favorite/add"';
    }
    ?>
           />
    <input type="submit" name="backToList" value="Retour à la liste" formaction="<?= $request->getBasePath() . '/favorite/list' ?>"/>
</form>
