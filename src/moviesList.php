<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/Manager.php';

$isUserAdmin = false;

session_start();
// si l'utilisateur est pas connecté et qu'il est amdinistrateur
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $isUserAdmin = true;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestion des cinémas - Films</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header><h1>Liste des films</h1></header>
        <table class="std">
            <tr>
                <th>Titre</th>
                <th>Titre original</th>
                <th colspan="3">Action</th>
            </tr>
            <?php
            // on récupère la liste des films ainsi que leurs informations
            $films = $fctManager->getMoviesList();
            // boucle de construction de la liste des cinémas
            foreach ($films as $film) {
                ?>
                <tr>
                    <td><?= $film['TITRE'] ?></td>
                    <td><?= $film['TITREORIGINAL'] ?></td>
                    <td>
                        <form name="movieShowtimes" action="movieShowtimes.php" method="GET">
                            <input name="filmID" type="hidden" value="<?= $film['FILMID'] ?>"/>
                            <input type="submit" value="Consulter les séances"/>
                        </form>
                    </td>
                    <?php if ($isUserAdmin): ?>
                        <td>
                            <form name="modifyMovie" action="editMovie.php" method="GET">
                                <input type="hidden" name="filmID" value="<?= $film['FILMID'] ?>"/>
                                <input type="image" src="images/modifyIcon.png" alt="Modify"/>
                            </form>
                        </td>
                        <td>
                            <form name="deleteMovie" action="deleteMovie.php" method="POST">
                                <input type="hidden" name="filmID" value="<?= $film['FILMID'] ?>"/>
                                <input type="image" src="images/deleteIcon.png" alt="Delete"/>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php
            }
            ?>
            <?php if ($isUserAdmin): ?>
                <tr class="new">
                    <td colspan="5">
                        <form name="addMovie" action="editMovie.php">
                            <button class="add" type="submit">Cliquer ici pour ajouter un film...</button>
                        </form>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <form name="backToMainPage" action="index.php">
            <input type="submit" value="Retour à l'accueil"/>
        </form>
    </body>
</html>
