<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/fctManager.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestion des cinémas - Cinémas</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header><h1>Liste des cinémas</h1></header>
        <main>
            <table class="std">
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Actions</th>
                </tr>
                <?php
                // on récupère la liste des cinémas ainsi que leurs informations
                $cinemas = $fctManager->getCinemasList();
                // boucle de construction de la liste des cinémas
                foreach ($cinemas as $cinema) {
                    ?>
                    <tr>
                        <td><?= $cinema['DENOMINATION'] ?></td>
                        <td><?= $cinema['ADRESSE'] ?></td>
                        <td>
                            <form name="cinemaShowtimes" action="cinemaShowtimes.php" method="GET">
                                <input name="cinemaID" type="hidden" value="<?= $cinema['CINEMAID'] ?>"/>
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
