<?php $this->titre = "Séances par cinéma"; ?>
<header>
    <h1>Séances du cinéma <?= $cinema->getDenomination(); ?></h1>
    <h2><?= $cinema->getAdresse(); ?></h2>
    <?php if ($filmsUnplanned) : ?>
        <form action="index.php" method="get">
            <fieldset>
                <legend>Ajouter un film à la programmation</legend>
                <input name="cinemaID" type="hidden" value="<?= $cinema->getCinemaId() ?>">
                <select name="filmID">
                    <?php
                    foreach ($filmsUnplanned as $film) :
                        ?>
                        <option value="<?= $film->getFilmId() ?>"><?= $film->getTitre() ?></option>
                        <?php
                    endforeach;
                    ?>    
                </select>
                <input name="action" type="hidden" value="editShowtime">
                <input name = "from" type = "hidden" value = "<?= $_SERVER['SCRIPT_NAME'] ?>">
                <button type = "submit">Ajouter</button>
            </fieldset>
        </form>
    <?php endif; ?>
</header>
<ul>
    <?php
// si au moins un résultat
    if (count($films) > 0) {
        // on boucle sur les résultats
        foreach ($films as $film) {
            ?>
            <li><h3><?= $film->getTitre() ?></h3></li>
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
                // boucle sur les séances
                foreach ($seances[$film->getFilmId()] as $seance) {
                    /*
                     * Formatage des dates
                     */
                    // nous sommes en Français
                    setlocale(LC_TIME, 'fra_fra');
                    // date du jour de projection de la séance
                    $jour         = $seance->getHeureDebut();
                    // On convertit pour un affichage en français
                    $jourConverti = utf8_encode(strftime('%d %B %Y',
                                    $jour->getTimestamp()));

                    $heureDebut = $seance->getHeureDebut()->format('H\hi');
                    $heureFin   = $seance->getHeureFin()->format('H\hi');
                    ?>
                    <tr>
                        <td><?= $jourConverti ?></td>
                        <td><?= $heureDebut ?></td>
                        <td><?= $heureFin ?></td>
                        <td><?= $seance->getVersion() ?></td>
                        <?php if ($adminConnected): ?>
                            <td>
                                <form name="modifyMovieShowtime" method="GET">
                                    <input type="hidden" name="action" value="editShowtime">
                                    <input type="hidden" name="cinemaID" value="<?= $cinema->getCinemaId() ?>"/>
                                    <input type="hidden" name="filmID" value="<?= $film->getFilmId() ?>"/>
                                    <input type="hidden" name="heureDebut" value="<?= $seance->getHeureDebut()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="heureFin" value="<?= $seance->getHeureFin()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="version" value="<?= $seance->getVersion() ?>"/>
                                    <input type="image" src="images/modifyIcon.png" alt="Modify"/>
                                    <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                </form>
                            </td>
                            <td>
                                <form name="deleteMovieShowtime" action="index.php?action=deleteShowtime" method="POST">
                                    <input type="hidden" name="cinemaID" value="<?= $cinema->getcinemaId() ?>"/>
                                    <input type="hidden" name="filmID" value="<?= $film->getFilmId() ?>"/>
                                    <input type="hidden" name="heureDebut" value="<?= $seance->getHeureDebut()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="heureFin" value="<?= $seance->getHeureFin()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="version" value="<?= $seance->getVersion() ?>"/>
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
                            <form action="index.php" method="get">
                                <input name="action" type="hidden" value="editShowtime">
                                <input name="cinemaID" type="hidden" value="<?= $cinema->getCinemaId() ?>">
                                <input name="filmID" type="hidden" value="<?= $film->getFilmId() ?>">
                                <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                <button class="add" type="submit">Cliquer ici pour ajouter une séance...</button>
                            </form>
                        </td>
                    </tr>
                <?php endif;
                ?>
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
