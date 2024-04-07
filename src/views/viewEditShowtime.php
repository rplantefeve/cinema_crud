<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestion des cinémas - Ajouter une séance</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header>
            <h1>Séances du cinéma <?= $cinema['DENOMINATION'] ?></h1>
            <h2>Pour le film <?= $film['TITRE'] ?></h2>
        </header>
        <form method="post">
            <fieldset>
                <label for="datedebut">Date de début : </label>
                <input id="datedebut" type="date" name="datedebut" placeholder="jj/mm/aaaa" value="<?= $seance['dateDebut'] ?>">
                <label for="heuredebut">Heure de début : </label>
                <input type="time" name="heuredebut" placeholder="hh:mm" value="<?= $seance['heureDebut'] ?>">

                <label for="datefin">Date de fin : </label>
                <input type="date" name="datefin" placeholder="jj/mm/aaaa" value="<?= $seance['dateFin'] ?>">
                <label for="heurefin">Heure de fin : </label>
                <input type="time" name="heurefin" placeholder="hh:mm" value="<?= $seance['heureFin'] ?>">
                <!-- les anciennes date et heure début et fin -->
                <input type="hidden" name="dateheurefinOld" value="<?= $seance['dateheureFinOld'] ?>">
                <input type="hidden" name="dateheuredebutOld" value="<?= $seance['dateheureDebutOld'] ?>">
                <label for="version">Version : </label>
                <select name="version">
                    <option value="VO" <?php
                    if ($seance['version'] == 'VO'): echo "selected";
                    endif;
            ?>>VO</option>
                    <option value="VF" <?php
            if ($seance['version'] == 'VF'): echo "selected";
            endif;
            ?>>VF</option>
                    <option value="VOSTFR" <?php
            if ($seance['version'] == 'VOSTFR'): echo "selected";
            endif;
            ?>>VOSTFR</option>
                </select>
                <input type="hidden" value="<?= $from ?>" name="from">
            </fieldset>
            <input type="hidden" name="cinemaID" value="<?= $cinemaID ?>">
            <input type="hidden" name="filmID" value="<?= $filmID ?>">
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
            <form action="cinemaShowtimes.php">
                <input name="cinemaID" type="hidden" value="<?= $cinemaID ?>">
                <button type="submit">Retour aux séances du cinéma</button>
            </form>
        <?php else: ?>
            <form action="movieShowtimes.php">
                <input name="filmID" type="hidden" value="<?= $filmID ?>">
                <button type="submit">Retour aux séances</button>
            </form>
        <?php endif; ?>
    </body>
</html>