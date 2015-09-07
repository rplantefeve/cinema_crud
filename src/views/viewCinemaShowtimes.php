<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestion des cinémas - Séances par cinéma</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header>
            <h1>Séances du cinéma <?= $cinema['DENOMINATION'] ?></h1>
            <h2><?= $cinema['ADRESSE'] ?></h2>
            <?php if ($filmsUnplanned and $adminConnected) : ?>
                <form action="editShowtime.php" method="get">
                    <fieldset>
                        <legend>Ajouter un film à la programmation</legend>
                        <input name="cinemaID" type="hidden" value="<?= $cinemaID ?>">
                        <select name="filmID">
                            <?php
                            foreach ($filmsUnplanned as $film) :
                                ?>
                                <option value="<?= $film['filmID'] ?>"><?= $film['titre'] ?></option>
                                <?php
                            endforeach;
                            ?>    
                        </select>
                        <input name = "from" type = "hidden" value = "<?= $_SERVER['SCRIPT_NAME'] ?>">
                        <button type = "submit">Ajouter</button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </header>
        <ul>
            <?php
            // si au moins un résultat
            if ($films !== null && count($films) > 0) {
                // on boucle sur les résultats
                foreach ($films as $film) {
                    ?>
                    <li><h3><?= $film['TITRE'] ?></h3></li>
                    <table class="std">
                        <tr>
                            <th>Date</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Version</th>
                            <?php if ($adminConnected): ?>
                                <th colspan="2">Action</th>
                            <?php endif; ?>
                        </tr>
                        <?php
                        // on récupère pour chaque film de ce cinéma, la liste des séances
                        $seances = $seancesMgr->getMovieShowtimes(
                            $cinemaID,
                                $film['FILMID']
                        );
                    // boucle sur les séances
                    foreach ($seances as $seance) {
                        /*
                         * Formatage des dates
                         */
                        // nous sommes en Français
                        setlocale(LC_TIME, 'fra_fra');
                        // date du jour de projection de la séance
                        $jour = new DateTime($seance['HEUREDEBUT']);
                        // On convertit pour un affichage en français
                        $jourConverti = utf8_encode(strftime(
                                '%d %B %Y',
                                            $jour->getTimestamp()
                            ));

                        $heureDebut = (new DateTime($seance['HEUREDEBUT']))->format('H\hi');
                        $heureFin = (new DateTime($seance['HEUREFIN']))->format('H\hi'); ?>
                            <tr>
                                <td><?= $jourConverti ?></td>
                                <td><?= $heureDebut ?></td>
                                <td><?= $heureFin ?></td>
                                <td><?= $seance['VERSION'] ?></td>
                                <?php if ($adminConnected): ?>
                                    <td>
                                        <form name="modifyMovieShowtime" action="editShowtime.php" method="GET">
                                            <input type="hidden" name="cinemaID" value="<?= $cinemaID ?>"/>
                                            <input type="hidden" name="filmID" value="<?= $film['FILMID'] ?>"/>
                                            <input type="hidden" name="heureDebut" value="<?= $seance['HEUREDEBUT'] ?>"/>
                                            <input type="hidden" name="heureFin" value="<?= $seance['HEUREFIN'] ?>"/>
                                            <input type="hidden" name="version" value="<?= $seance['VERSION'] ?>"/>
                                            <input type="image" src="images/modifyIcon.png" alt="Modify"/>
                                            <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                        </form>
                                    </td>
                                    <td>
                                        <form name="deleteMovieShowtime" action="deleteShowtime.php" method="POST">
                                            <input type="hidden" name="cinemaID" value="<?= $cinemaID ?>"/>
                                            <input type="hidden" name="filmID" value="<?= $film['FILMID'] ?>"/>
                                            <input type="hidden" name="heureDebut" value="<?= $seance['HEUREDEBUT'] ?>"/>
                                            <input type="hidden" name="heureFin" value="<?= $seance['HEUREFIN'] ?>"/>
                                            <input type="hidden" name="version" value="<?= $seance['VERSION'] ?>"/>
                                            <input type="image" src="images/deleteIcon.png" alt="Delete"/>
                                            <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>

                            <?php
                    }
                    if ($adminConnected):
                            ?>
                            <tr class="new">
                                <td colspan="6">
                                    <form action="editShowtime.php" method="get">
                                        <input name="cinemaID" type="hidden" value="<?= $cinemaID ?>">
                                        <input name="filmID" type="hidden" value="<?= $film['FILMID'] ?>">
                                        <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                        <button class="add" type="submit">Cliquer ici pour ajouter une séance...</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                    <br>
                    <?php
                } // fin de la boucle de parcours des films
            } // fin du if au moins un film
            ?>
        </ul>
        <form name="cinemasList" method="GET" action="index.php">
            <input name="action" type="hidden" value="cinemasList"/>
            <input type="submit" value="Retour à la liste des cinémas"/>
        </form>
    </body>
</html>