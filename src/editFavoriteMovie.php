<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/includes/Manager.php';

// TODO
?>
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
            <select name="filmID">
                <?php
                // TODO
                ?>
            </select>
            <div class="error">
                <?php
                // TODO
                ?>
            </div>
            <label>Commentaire :</label>
            <textarea name="comment"><?php //TODO?></textarea>
            <br/>
            <input type="hidden" value="<?php //TODO?>" name="userID"/>
            <input type="submit" name="saveEntry" value="Sauvegarder"/>
            <input type="submit" name="backToList" value="Retour à la liste"/>
        </form>
    </body>
</html>
