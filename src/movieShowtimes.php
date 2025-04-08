<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/fctManager.php';

// si la méthode de formulaire est la méthode GET
if (filter_input(
    INPUT_SERVER,
                'REQUEST_METHOD'
) === "GET") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(
        INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT]
    );
    // si l'identifiant du film a bien été passé en GET'
    if ($sanitizedEntries && $sanitizedEntries['filmID'] !== null && $sanitizedEntries['filmID'] !== '') {
        // on récupère l'identifiant du cinéma
        $filmID = $sanitizedEntries['filmID'];
        // puis on récupère les informations du film en question
        $film = $fctManager->getMovieInformationsByID($filmID);
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
<html>
    <head>
        <meta charset="UTF-8">
        <title>Gestion des cinémas - Séances par film</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header>
            <h1>Séances du film &laquo;&nbsp;<?= $film['TITRE'] ?>&nbsp;&raquo;</h1>
            <h2><?= $film['TITREORIGINAL'] ?></h2>
        </header>
        <main>
            <ul>
                <?php
                // on récupère la liste des cinémas de ce film
                $cinemas = $fctManager->getMovieCinemasByMovieID($filmID);
                // on boucle sur les résultats
                foreach ($cinemas as $cinema) {
                    ?>
                    <li><?= $cinema['DENOMINATION'] ?></li>
                    <ul>
                        <?php
                        // on récupère pour chaque cinéma de ce film, la liste des séances
                        $seances = $fctManager->getMovieShowtimes(
                            $cinema['CINEMAID'],
                                $filmID
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
                            <li>Séance du <?= $jourConverti ?>. Heure de début : <?= $heureDebut ?>. Heure de fin : <?= $heureFin ?>. Version : <?= $seance['VERSION'] ?></li>
                            <?php
                    } ?>
                    </ul>
                    <?php
                }
                ?>
            </ul>
            <form action="moviesList.php">
                <button type="submit">Retour à la liste des films</button>
            </form>
        </main>
        <footer>
            <span class="copyleft">&copy;</span> 2025 Gestion de Cinéma. Tous droits inversés.
        </footer>
    </body>
</html>
