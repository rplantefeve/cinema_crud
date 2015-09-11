<?php $this->titre = 'Séances par film'; ?>
<header>
    <h1>Séances du film <?= $film['TITRE'] ?></h1>
    <h2><?= $film['TITREORIGINAL'] ?></h2>
</header>
<ul>
    <?php
    // on boucle sur les résultats
    foreach ($cinemas as $cinema) {
        ?>
        <li><?= $cinema->getDenomination(); ?></li>
        <ul>
            <?php
            // boucle sur les séances
            foreach ($seances[$cinema->getCinemaId()] as $seance) {
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
<form method="GET" action="index.php">
    <input name="action" type="hidden" value="moviesList"/>
    <input type="submit" value="Retour à la liste des films"/>
</form>
