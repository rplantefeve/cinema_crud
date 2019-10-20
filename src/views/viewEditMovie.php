<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Film - Editer un film</title>
        <link rel="stylesheet" type="text/css" href="css/cinema.css"/>
    </head>
    <body>
        <h1>Ajouter/Modifier un film</h1>
        <form method="POST" name="editCinema" action="editMovie.php">
            <label>Titre :</label>
            <input name="titre" type="text" value="<?= $film['TITRE'] ?>" required/>
            <label>Titre original :</label>
            <input name="titreOriginal" type="text" value="<?= $film['TITREORIGINAL'] ?>" required/>
            <br/>
            <input type="hidden" value="<?= $film['FILMID'] ?>" name="filmID"/>
            <?php
            // si c'est une modification, c'est une information dont nous avons besoin
            if (!$isItACreation) {
                ?>
                <input type="hidden" name="modificationInProgress" value="true"/>
                <?php
            }
            ?>
            <input type="submit" name="saveEntry" value="Sauvegarder"/>
            <input type="submit" name="backToList" value="Retour à la liste"/>
        </form>
    </body>
</html>