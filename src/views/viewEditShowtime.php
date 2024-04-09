<?php
$this->title = "Gestion des cinémas - Ajouter une séance";
?>
<header>
    <h1>Séances du cinéma <?= $cinema->getDenomination() ?></h1>
    <h2>Pour le film <?= $film->getTitre() ?></h2>
</header>
<form method="post">
    <fieldset>
        <label for="datedebut">Date de début : </label>
        <input id="datedebut" type="date" name="datedebut" placeholder="jj/mm/aaaa" value="<?php
               if ($seance): echo $seance->getHeureDebut()->format('Y-m-d');
               endif;
               ?>">
        <label for="heuredebut">Heure de début : </label>
        <input type="time" name="heuredebut" placeholder="hh:mm" value="<?php
        if ($seance): echo $seance->getHeureDebut()->format('H:i');
        endif;
        ?>">

        <label for="datefin">Date de fin : </label>
        <input type="date" name="datefin" placeholder="jj/mm/aaaa" value="<?php
        if ($seance): echo $seance->getHeureFin()->format('Y-m-d');
        endif;
        ?>">
        <label for="heurefin">Heure de fin : </label>
        <input type="time" name="heurefin" placeholder="hh:mm" value="<?php
        if ($seance) : echo $seance->getHeureFin()->format('H:i');
        endif;
        ?>">
        <!-- les anciennes date et heure début et fin -->
        <input type="hidden" name="dateheurefinOld" value="<?= $seanceOld['dateheureFinOld'] ?>">
        <input type="hidden" name="dateheuredebutOld" value="<?= $seanceOld['dateheureDebutOld'] ?>">
        <label for="version">Version : </label>
        <select name="version">
            <option value="VO" <?php
            if ($seance && $seance->getVersion() == 'VO'): echo "selected";
            endif;
            ?>>VO</option>
            <option value="VF" <?php
            if ($seance && $seance->getVersion() == 'VF'): echo "selected";
            endif;
            ?>>VF</option>
            <option value="VOSTFR" <?php
            if ($seance && $seance->getVersion() == 'VOSTFR'): echo "selected";
            endif;
            ?>>VOSTFR</option>
        </select>
        <input type="hidden" value="<?= $from ?>" name="from">
    </fieldset>
    <input type="hidden" name="cinemaID" value="<?= $cinema->getCinemaId() ?>">
    <input type="hidden" name="filmID" value="<?= $film->getFilmId() ?>">
    <?php
    // si c'est une modification, c'est une information dont nous avons besoin
    if (!$isItACreation) {
        ?>
        <input type="hidden" name="modificationInProgress" value="true"/>
        <?php
    }
    ?>
    <button type="submit">Sauvegarder</button>
</form>
<?php if ($fromCinema): ?>
    <form method="get">
        <input name="action" value="cinemaShowtimes" type="hidden">
        <input name="cinemaID" type="hidden" value="<?= $cinema->getCinemaId() ?>">
        <button type="submit">Retour aux séances du cinéma</button>
    </form>
<?php else: ?>
    <form method="get" action="index.php">
        <input name="action" type="hidden" value="movieShowtimes">
        <input name="filmID" type="hidden" value="<?= $film->getFilmId() ?>">
        <button type="submit">Retour aux séances du film</button>
    </form>
<?php endif; ?>
