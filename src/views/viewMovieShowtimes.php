<?php
 $this->titre = 'Séances par film'; 
$path        = $request->getBasePath();
?>
<header>
    <h1>Séances du film <?= $film->getTitre() ?></h1>
    <h2><?= $film->getTitreOriginal() ?></h2>
    <?php if ($adminConnected && $cinemasUnplanned) : ?>
        <form action="<?= $path . '/showtime/movie/add/' . $film->getFilmId() ?>" method="get">
            <fieldset>
                <legend>Programmer le film dans un cinéma</legend>
                <select name="cinemaID">
                    <?php
                    foreach ($cinemasUnplanned as $cinema) :
                        ?>
                        <option value="<?= $cinema->getCinemaId() ?>"><?= $cinema->getDenomination() ?></option>
                        <?php
                    endforeach;
                    ?>    
                </select>
                <input name = "from" type = "hidden" value = "movie">
                <button type = "submit">Ajouter</button>
            </fieldset>
        </form>
    <?php endif; ?>
</header>
<ul>
    <?php
    if (count($cinemas) > 0):
        // on boucle sur les résultats
        foreach ($cinemas as $cinema) {
            ?>
            <li><h3><?= $cinema->getDenomination() ?></h3></li>
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
                foreach ($seances[$cinema->getCinemaId()] as $seance) {
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
                                <form name="modifyMovieShowtime" action="<?= $path . '/showtime/edit/' . $film->getFilmId() . '/' . $cinema->getCinemaId() ?>" method="GET">
                                    <input type="hidden" name="heureDebut" value="<?= $seance->getHeureDebut()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="heureFin" value="<?= $seance->getHeureFin()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="version" value="<?= $seance->getVersion() ?>"/>
                                    <input type="submit" id="modify" value=""/>
                                    <input name="from" type="hidden" value="movie">
                                </form>
                            </td>
                            <td>
                                <form name="deleteMovieShowtime" action="<?= $path . '/showtime/delete/' . $film->getFilmId() . '/' . $cinema->getcinemaId() ?>" method="POST">
                                    <input type="hidden" name="heureDebut" value="<?= $seance->getHeureDebut()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="heureFin" value="<?= $seance->getHeureFin()->format('Y-m-d H:i') ?>"/>
                                    <input type="hidden" name="version" value="<?= $seance->getVersion() ?>"/>
                                    <input type="image" src="<?= $path ?>/images/deleteIcon.png" alt="Delete"/>
                                    <input name="from" type="hidden" value="movie">
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
                            <form action="<?= $path . '/showtime/add/' . $film->getFilmId() . '/' . $cinema->getCinemaId() ?>" method="get">
                                <input name="from" type="hidden" value="movie">
                                <button class="add" type="submit">Cliquer ici pour ajouter une séance...</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                endif;
                ?>  
            </table>
            <br>
            <?php
        } // fin de la boucle
    endif;
    ?>
</ul>
<form name="moviesList" method="GET" action="<?= $path . '/movie/list' ?>">
    <input type="submit" value="Retour à la liste des films"/>
</form>
