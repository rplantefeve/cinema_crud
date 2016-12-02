<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . './includes/Manager.php';

// si la méthode de formulaire est la méthode GET
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "GET") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT]);
    // si l'identifiant du film a bien été passé en GET'
    if ($sanitizedEntries && $sanitizedEntries['filmID'] !== NULL && $sanitizedEntries['filmID'] !==
            '') {
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
            <h1>Séances du film <?= $film['TITRE'] ?></h1>
            <h2><?= $film['TITREORIGINAL'] ?></h2>
        </header>
        <ul>
            <?php
            // on récupère la liste des cinémas de ce film
            $cinemas = $fctManager->getMovieCinemasByMovieID($filmID);
            if (count($cinemas) > 0):
                // on boucle sur les résultats
                foreach ($cinemas as $cinema) {
                    ?>
                    <li><h3><?= $cinema['DENOMINATION'] ?></h3></li>
                    <ul>
                        <?php
                        // on récupère pour chaque cinéma de ce film, la liste des séances
                        $seances = $fctManager->getMovieShowtimes($cinema['CINEMAID'],
                                $filmID);
                        // boucle sur les séances
                        foreach ($seances as $seance) {
                            /*
                             * Formatage des dates
                             */
                            // nous sommes en Français
                            setlocale(LC_TIME, 'fra_fra');
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
                    <br>
                    <form action="editShowtime.php" method="get">
                        <input name="cinemaID" type="hidden" value="<?= $cinema['CINEMAID'] ?>">
                        <input name="filmID" type="hidden" value="<?= $filmID ?>">
                        <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                        <button type="submit">Ajouter une séance</button>
                    </form>
                    <?php
                } // fin de la boucle
            endif;
            ?>
        </ul>
        <form action="moviesList.php">
            <input type="submit" value="Retour à la liste des films"/>
        </form>
    </body>
</html>
