<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . './includes/fctManager.php';

// si la méthode de formulaire est la méthode GET
if (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "GET") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT]);

    // si l'identifiant du cinéma a bien été passé en GET
    if ($sanitizedEntries && $sanitizedEntries['cinemaID'] !== NULL && $sanitizedEntries['cinemaID'] != '') {
        // on récupère l'identifiant du cinéma
        $cinemaID = $sanitizedEntries['cinemaID'];
        // puis on récupère les informations du cinéma en question
        $cinema = $fctManager->getCinemaInformationsByID($cinemaID);
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
        <title>Gestion des cinémas - Séances par cinéma</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <header>
            <h1>Séances du cinéma <?= $cinema['DENOMINATION'] ?></h1>
            <h2><?= $cinema['ADRESSE'] ?></h2>
        </header>
        <ul>
            <?php
            // on récupère la liste des films de ce cinéma
            $films = $fctManager->getCinemaMoviesByCinemaID($cinemaID);
            // on boucle sur les résultats
            foreach ($films as $film) {
                ?>
                <li><?= $film['TITRE'] ?></li>
                <ul>
                    <?php
                    // on récupère pour chaque film de ce cinéma, la liste des séances
                    $seances = $fctManager->getMovieShowtimes($cinemaID,
                            $film['FILMID']);
                    // boucle sur les séances
                    foreach ($seances as $seance) {
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
        <form action="cinemasList.php">
            <input type="submit" value="Retour à la liste des cinémas"/>
        </form>
    </body>
</html>
