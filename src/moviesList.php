<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/fctManager.php';
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
        <main>
            <table class="std">
                <tr>
                    <th>Titre</th>
                    <th>Titre original</th>
                    <th>Actions</th>
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
                                <button type="submit">Consulter les séances</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <form name="backToMainPage" action="index.php">
                <button type="submit">Retour à l'accueil</button>
            </form>
        </main>
        <footer>
            <span class="copyleft">&copy;</span> 2025 Gestion de Cinéma. Tous droits inversés.
        </footer>
    </body>
</html>
