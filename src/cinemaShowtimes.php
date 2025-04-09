<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/Manager.php';

$adminConnected = false;

session_start();
// si l'utilisateur admin est connecté
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $adminConnected = true;
}

// si la méthode de formulaire est la méthode GET
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {

    // on assainie les entrées
    $sanitizedEntries = filter_input_array(
        INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]
    );

    // si l'identifiant du cinéma a bien été passé en GET
    if ($sanitizedEntries && $sanitizedEntries['cinemaID'] !== null && $sanitizedEntries['cinemaID'] != '') {
        // on récupère l'identifiant du cinéma
        $cinemaID = $sanitizedEntries['cinemaID'];
        // puis on récupère les informations du cinéma en question
        $cinema = $fctManager->getCinemaInformationsByID($cinemaID);
        // on récupère les films pas encore projetés
        $filmsUnplanned = $fctManager->getNonPlannedMovies($cinemaID);
    }
    // sinon, on retourne à l'accueil
    else {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Gestion des cinémas - Séances par cinéma</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet">
    </head>
    <body>
        <header>
            <h1>Séances du cinéma <?= $cinema['DENOMINATION'] ?></h1>
            <h2><?= $cinema['ADRESSE'] ?></h2>
        </header>
        <main>
            <?php if ($filmsUnplanned and $adminConnected) : ?>
                <div class="mainbox">
                    <div>
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
                    </div>
                </div>
            <?php endif; ?>
            <div class="mainbox">
                <div>
                    <ul>
                        <?php
                        // on récupère la liste des films de ce cinéma
                        $films = $fctManager->getCinemaMoviesByCinemaID($cinemaID) ?? [];
                        // si au moins un résultat
                        if (count($films) > 0) {
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
                                        $seances = $fctManager->getMovieShowtimes(
                                        
                                        $cinemaID,
                                                $film['FILMID']
                                    
                                    );
                                // boucle sur les séances
                                foreach ($seances as $seance) {
                                    /*
                                    * Formatage des dates
                                    */
                                    // nous sommes en Français
                                    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                                    // date du jour de projection de la séance
                                    $jour = new DateTime($seance['HEUREDEBUT']);
                                    // On convertit pour un affichage en français
                                    $jourConverti = $formatter->format($jour->getTimestamp());

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
                    <br>
                    <form action = "cinemasList.php">
                        <button type = "submit">Retour à la liste des cinémas</button>
                    </form>
                </div>
            </div>
        </main>
        <footer>
            <span class="copyleft">&copy;</span> 2025 Gestion de Cinéma. Tous droits inversés.
        </footer>
    </body>
</html>
