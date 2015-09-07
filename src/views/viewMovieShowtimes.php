<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestion des cinémas - Séances par film</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header>
            <h1>Séances du film <?= $film['TITRE'] ?></h1>
            <h2><?= $film['TITREORIGINAL'] ?></h2>
        </header>
        <ul>
            <?php
            // on boucle sur les résultats
            foreach ($cinemas as $cinema) {
                ?>
                <li><?= $cinema['DENOMINATION'] ?></li>
                <ul>
                    <?php
                    // on récupère pour chaque cinéma de ce film, la liste des séances
                    $seances = $seancesMgr->getMovieShowtimes($cinema['CINEMAID'],
                            $filmID);
                    // boucle sur les séances
                    foreach ($seances as $seance) {
                        /*
                         * Formatage des dates
                         */
                        // nous sommes en Français
                        setlocale(LC_TIME,
                                'fra_fra');
                        // date du jour de projection de la séance
                        $jour = new DateTime($seance['HEUREDEBUT']);
                        // On convertit pour un affichage en français
                        $jourConverti = utf8_encode(strftime('%d %B %Y',
                                        $jour->getTimestamp()));

                        $heureDebut = (new DateTime($seance['HEUREDEBUT']))->format('H\hi');
                        $heureFin = (new DateTime($seance['HEUREFIN']))->format('H\hi');
                        ?>
                        <li>Séance du <?= $jourConverti ?>. Heure de début : <?= $heureDebut ?>. Heure de fin : <?= $heureFin ?>. Version : <?= $seance['VERSION'] ?></li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
            }
            ?>
        </ul>
        <form action="moviesList.php">
            <input type="submit" value="Retour à la liste des films"/>
        </form>
    </body>
</html>
