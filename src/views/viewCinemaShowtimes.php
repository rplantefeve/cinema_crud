<?php $this->titre = "Séances par cinéma"; ?>
<header>
    <h1>Séances du cinéma <?= $cinema->getDenomination(); ?></h1>
    <h2><?= $cinema->getAdresse(); ?></h2>
</header>
<ul>
    <?php
    // on boucle sur les résultats
    foreach ($films as $film) {
        ?>
        <li><?= $film->getTitre() ?></li>
        <ul>
            <?php
            // boucle sur les séances
            foreach ($seances[$film->getFilmId()] as $seance) {
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
<form name="cinemasList" method="GET" action="index.php">
    <input name="action" type="hidden" value="cinemasList"/>
    <input type="submit" value="Retour à la liste des cinémas"/>
</form>
