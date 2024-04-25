<?php
$this->titre = "Séances par cinéma";
$path        = $request->getBasePath();
?>
<header>
    <h1>Séances du cinéma <?= $cinema->getDenomination(); ?></h1>
    <h2><?= $cinema->getAdresse(); ?></h2>
    <?php if ($adminConnected === true && $filmsUnplanned !== null) : ?>
        <form action="<?= $path . '/showtime/cinema/add/' . $cinema->getCinemaId() ?>" method="get">
            <fieldset>
                <legend>Ajouter un film à la programmation</legend>
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
                <input name = "from" type = "hidden" value = "cinema">
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
            <li><h3><?= $film->getTitre() ?></h3></li>
            <table class="showtime">
                <tr>
                    <th>Date</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Version</th>
                    <?php if ($adminConnected === true) : ?>
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
                    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                    // date du jour de projection de la séance
                    $jour = $seance->getHeureDebut();
                    // On convertit pour un affichage en français
                    $jourConverti = $formatter->format($jour->getTimestamp());

                    $heureDebut = $seance->getHeureDebut()->format('H\hi');
                    $heureFin = $seance->getHeureFin()->format('H\hi');
                    ?>
                    <tr>
                        <td><?= $jourConverti ?></td>
                        <td><?= $heureDebut ?></td>
                        <td><?= $heureFin ?></td>
                        <td><?= $seance->getVersion() ?></td>
                        <?php if ($adminConnected === true) : ?>
                            <td>
                                <form name="modifyMovieShowtime" action="<?= $path . '/showtime/edit/' . $film->getFilmId() . '/' . $cinema->getCinemaId() ?>" method="GET">
                                    <input type="hidden" name="heureDebut" value="<?= $seance->getHeureDebut()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="heureFin" value="<?= $seance->getHeureFin()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="version" value="<?= $seance->getVersion() ?>"/>
                                    <input type="submit" id="modify" value=""/>
                                    <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                </form>
                            </td>
                            <td>
                                <form name="deleteMovieShowtime" action="<?= $path . '/showtime/delete/' . $film->getFilmId() . '/' . $cinema->getcinemaId() ?>" method="POST">
                                    <input type="hidden" name="heureDebut" value="<?= $seance->getHeureDebut()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="heureFin" value="<?= $seance->getHeureFin()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="version" value="<?= $seance->getVersion() ?>"/>
                                    <input type="image" src="<?= $path ?>/images/deleteIcon.png" alt="Delete"/>
                                    <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>

                    <?php
                }
                if ($adminConnected === true) :
                    ?>
                    <tr class="new">
                        <td colspan="6">
                            <form action="<?= $path . '/showtime/add/' . $film->getFilmId() . '/' . $cinema->getCinemaId() ?>" method="get">
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
<form name="cinemasList" method="GET" action="<?= $path . '/cinema/list' ?>">
    <input type="submit" value="Retour à la liste des cinémas"/>
</form>