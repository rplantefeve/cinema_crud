<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . './includes/Manager.php';

$adminConnected = false;

session_start();
// si l'utilisateur admin est connexté
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $adminConnected = true;
}

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
                            <tr>
                                <td><?= $jourConverti ?></td>
                                <td><?= $heureDebut ?></td>
                                <td><?= $heureFin ?></td>
                                <td><?= $seance['VERSION'] ?></td>
                                <?php if ($adminConnected): ?>
                                    <td>
                                        <form name="modifyMovieShowtime" action="editShowtime.php" method="GET">
                                            <input type="hidden" name="cinemaID" value="<?= $cinema['CINEMAID'] ?>"/>
                                            <input type="hidden" name="filmID" value="<?= $filmID ?>"/>
                                            <input type="hidden" name="heureDebut" value="<?= $seance['HEUREDEBUT'] ?>"/>
                                            <input type="hidden" name="heureFin" value="<?= $seance['HEUREFIN'] ?>"/>
                                            <input type="hidden" name="version" value="<?= $seance['VERSION'] ?>"/>
                                            <input type="image" src="images/modifyIcon.png" alt="Modify"/>
                                            <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                                        </form>
                                    </td>
                                    <td>
                                        <form name="deleteMovieShowtime" action="deleteShowtime.php" method="POST">
                                            <input type="hidden" name="cinemaID" value="<?= $cinema['CINEMAID'] ?>"/>
                                            <input type="hidden" name="filmID" value="<?= $filmID ?>"/>
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
                        ?>  
                    </table>
                    <br>
                    <?php if ($adminConnected): ?>
                        <form action="editShowtime.php" method="get">
                            <input name="cinemaID" type="hidden" value="<?= $cinema['CINEMAID'] ?>">
                            <input name="filmID" type="hidden" value="<?= $filmID ?>">
                            <input name="from" type="hidden" value="<?= $_SERVER['SCRIPT_NAME'] ?>">
                            <button type="submit">Ajouter une séance</button>
                        </form>
                        <?php
                    endif;
                } // fin de la boucle
            endif;
            ?>
        </ul>
        <form action="moviesList.php">
            <input type="submit" value="Retour à la liste des films"/>
        </form>
    </body>
</html>
