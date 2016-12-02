<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . './includes/Manager.php';

session_start();
// si l'utilisateur n'est pas connecté ou sinon s'il n'est pas amdinistrateur
if (!array_key_exists("user", $_SESSION) or $_SESSION['user'] !== 'admin@adm.adm') {
// renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// init. des flags. Etat par défaut => je viens du cinéma et je créé
$fromCinema = true;
$fromFilm = false;
$isItACreation = true;

// init. des variables du formulaire
$seance = ['dateDebut' => '',
    'heureDebut' => '',
    'dateFin' => '',
    'heureFin' => '',
    'dateDebutOld' => '',
    'heureDebutOld' => '',
    'dateFinOld' => '',
    'heureFinOld' => '',
    'version' => ''];

// si l'on est en GET
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'GET') {
    // on assainie les variables
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'from' => FILTER_SANITIZE_STRING,
        'heureDebut' => FILTER_SANITIZE_STRING,
        'heureFin' => FILTER_SANITIZE_STRING,
        'version' => FILTER_SANITIZE_STRING]);
    // pour l'instant, on vérifie les données en GET
    if ($sanitizedEntries && isset($sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'], $sanitizedEntries['from'])) {
        // on récupère l'identifiant du cinéma
        $cinemaID = $sanitizedEntries['cinemaID'];
        // l'identifiant du film
        $filmID = $sanitizedEntries['filmID'];
        // d'où vient on ?
        $from = $sanitizedEntries['from'];
        // puis on récupère les informations du cinéma en question
        $cinema = $fctManager->getCinemaInformationsByID($cinemaID);
        // puis on récupère les informations du film en question
        $film = $fctManager->getMovieInformationsByID($filmID);

        // s'il on vient des séances du film
        if (strstr($sanitizedEntries['from'], 'movie')) {
            $fromCinema = false;
            // on vient du film
            $fromFilm = true;
        }

        // ici, on veut savoir si on modifie ou si on ajoute
        if (isset($sanitizedEntries['heureDebut'],
                        $sanitizedEntries['heureFin'],
                        $sanitizedEntries['version'])) {
            // nous sommes dans le cas d'une modification
            $isItACreation = false;
            // on récupère les anciennes valeurs (utile pour retrouver la séance avant de la modifier
            $seance['dateheureDebutOld'] = $sanitizedEntries['heureDebut'];
            $seance['dateheureFinOld'] = $sanitizedEntries['heureFin'];
            // dates PHP
            $dateheureDebut = new DateTime($sanitizedEntries['heureDebut']);
            $dateheureFin = new DateTime($sanitizedEntries['heureFin']);
            // découpage en heures
            $seance['heureDebut'] = $dateheureDebut->format("H:i");
            $seance['heureFin'] = $dateheureFin->format("H:i");
            // découpage en jour/mois/année
            $seance['dateDebut'] = $dateheureDebut->format("d/m/Y");
            $seance['dateFin'] = $dateheureFin->format("d/m/Y");
            // on récupère la version
            $seance['version'] = $sanitizedEntries['version'];
        }
    }
    // sinon, on retourne à l'accueil
    else {
        header('Location: index.php');
        exit();
    }
// sinon, on est en POST
} else if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
    // on assainie les variables
    $sanitizedEntries = filter_input_array(INPUT_POST,
            ['cinemaID' => FILTER_SANITIZE_NUMBER_INT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'datedebut' => FILTER_SANITIZE_STRING,
        'heuredebut' => FILTER_SANITIZE_STRING,
        'datefin' => FILTER_SANITIZE_STRING,
        'heurefin' => FILTER_SANITIZE_STRING,
        'dateheurefinOld' => FILTER_SANITIZE_STRING,
        'dateheuredebutOld' => FILTER_SANITIZE_STRING,
        'version' => FILTER_SANITIZE_STRING,
        'from' => FILTER_SANITIZE_STRING,
        'modificationInProgress' => FILTER_SANITIZE_STRING]);
    // si toutes les valeurs sont renseignées
    if ($sanitizedEntries && isset($sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'], $sanitizedEntries['datedebut'],
                    $sanitizedEntries['heuredebut'],
                    $sanitizedEntries['datefin'], $sanitizedEntries['heurefin'],
                    $sanitizedEntries['dateheuredebutOld'],
                    $sanitizedEntries['dateheurefinOld'],
                    $sanitizedEntries['version'], $sanitizedEntries['from'])) {
        // nous sommes en Français
        setlocale(LC_TIME, 'fra_fra');
        // date du jour de projection de la séance
        $datetimeDebut = new DateTime($sanitizedEntries['datedebut'] . ' ' . $sanitizedEntries['heuredebut']);
        $datetimeFin = new DateTime($sanitizedEntries['datefin'] . ' ' . $sanitizedEntries['heurefin']);
        // Est-on dans le cas d'une insertion ?
        if (!isset($sanitizedEntries['modificationInProgress'])) {
            // j'insère dans la base
            $resultat = $fctManager->insertNewShowtime($sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'],
                    $datetimeDebut->format("Y-m-d H:i"),
                    $datetimeFin->format("Y-m-d H:i"),
                    $sanitizedEntries['version']);
        } else {
            // c'est une mise à jour
            $resultat = $fctManager->updateShowtime($sanitizedEntries['cinemaID'],
                    $sanitizedEntries['filmID'],
                    $sanitizedEntries['dateheuredebutOld'],
                    $sanitizedEntries['dateheurefinOld'],
                    $datetimeDebut->format("Y-m-d H:i"),
                    $datetimeFin->format("Y-m-d H:i"),
                    $sanitizedEntries['version']);
        }
        // en fonction d'où je viens, je redirige
        if (strstr($sanitizedEntries['from'], 'movie')) {
            header('Location: movieShowtimes.php?filmID=' . $sanitizedEntries['filmID']);
            exit;
        } else {
            header('Location: cinemaShowtimes.php?cinemaID=' . $sanitizedEntries['cinemaID']);
            exit;
        }
    }
}
// sinon, on retourne à l'accueil
else {
    header('Location: index.php');
    exit();
}
?>
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
                <input id="datedebut" type="text" name="datedebut" placeholder="jj/mm/aaaa" value="<?= $seance['dateDebut'] ?>">
                <label for="heuredebut">Heure de début : </label>
                <input type="text" name="heuredebut" placeholder="hh:mm" value="<?= $seance['heureDebut'] ?>">

                <label for="datefin">Date de fin : </label>
                <input type="text" name="datefin" placeholder="jj/mm/aaaa" value="<?= $seance['dateFin'] ?>">
                <label for="heurefin">Heure de fin : </label>
                <input type="text" name="heurefin" placeholder="hh:mm" value="<?= $seance['heureFin'] ?>">
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
