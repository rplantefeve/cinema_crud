<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Espace Personnel - Editer un film préféré</title>
        <link rel="stylesheet" type="text/css" href="css/cinema.css"/>
    </head>
    <body>
        <form method="POST" name="editFavoriteMovie" action="editFavoriteMovie.php">
            <label>Titre :</label>
            <select name="filmID" <?php
            if ($isItACreation === false) :
                echo "disabled";
            endif;
            ?>>
                        <?php
                        // si c'est une création, on crée la liste des films dynamiquement
                        if ($isItACreation === true) {
                            // s'il y a des résultats
                            if ($films !== null) {
                                foreach ($films as $film) {
                                    ?>
                            <option value="<?= $film['filmID'] ?>"><?= $film['titre'] ?></option>
                                    <?php
                                }
                            }
                        } else { // sinon, c'est une modification, nous n'avons qu'une seule option dans la liste
                            ?>
                    <option selected="selected" value="<?= $preference['filmID'] ?>"><?= $preference['titre'] ?></option>
                            <?php
                        }
                        ?>
            </select>
            <div class="error">
                <?php
                if ($aFilmIsSelected === false) {
                    echo "Veuillez renseigner un titre de film.";
                }
                ?>
            </div>
            <label>Commentaire :</label>
            <textarea name="comment"><?= $preference['commentaire'] ?></textarea>
            <br/>
            <input type="hidden" value="<?= $preference['userID'] ?>" name="userID"/>
            <?php
            // si c'est une modification, c'est une information dont nous avons besoin
            if ($isItACreation === false) {
                ?>
                <input type="hidden" name="modificationInProgress" value="true"/>
                <input type="hidden" name="filmID" value="<?= $preference['filmID'] ?>"/>
                <?php
            }
            ?>
            <input type="submit" name="saveEntry" value="Sauvegarder"/>
            <input type="submit" name="backToList" value="Retour à la liste"/>
        </form>
    </body>
</html>
