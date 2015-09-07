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
            </tr>
            <?php
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
                </tr>
                <?php
            }
            ?>
        </table>
        <form name="backToMainPage" action="index.php">
            <input type="submit" value="Retour à l'accueil"/>
        </form>
    </body>
</html>
