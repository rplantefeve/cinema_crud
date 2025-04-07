<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/includes/fctManager.php';

session_start();
// si l'utilisateur n'est pas connecté
if (!array_key_exists("user", $_SESSION)) {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// variable de contrôle de formulaire
$aFilmIsSelected = true;

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === "POST") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(
        INPUT_POST,
        ['backToList' => FILTER_DEFAULT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'userID' => FILTER_SANITIZE_NUMBER_INT,
        'comment' => FILTER_DEFAULT]
    );

    // si l'action demandée est retour en arrière
    if (array_key_exists("backToList", $sanitizedEntries) 
        && $sanitizedEntries['backToList'] !== null) {
        // on redirige vers la page d'édition des films favoris
        header('Location: editFavoriteMoviesList.php');
        exit;
    }
    // sinon (l'action demandée est la sauvegarde d'un favori)
    else {
        // si un film a été selectionné
        if (array_key_exists(
 
            'filmID',
                        $sanitizedEntries
 
        ) && $sanitizedEntries['filmID'] !== null) {

            // on ajoute la préférence de l'utilisateur
            $fctManager->insertNewFavoriteMovie(
                $sanitizedEntries['userID'],
                    $sanitizedEntries['filmID'],
                    $sanitizedEntries['comment']
            );

            // on revient à la liste des préférences
            header('Location: editFavoriteMoviesList.php');
            exit;
        }
        // sinon (un film n'a pas été sélectionné)
        else {
            //
            $aFilmIsSelected = false;
            // initialisation des champs du formulaire
            $preference = [
                "userID" => $sanitizedEntries["userID"],
                "filmID" => "",
                "titre" => "",
                "commentaire" => $sanitizedEntries["comment"]];
            $userID = $sanitizedEntries['userID'];
        }
    }
}
// sinon, c'est une création
else {
    // on initialise les autres variables de formulaire à vide
    $preference = [
        "userID" => $_SESSION['userID'],
        "filmID" => "",
        "titre" => "",
        "commentaire" => ""];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Espace Personnel - Editer un film préféré</title>
        <link rel="stylesheet" type="text/css" href="css/cinema.css"/>
    </head>
    <body>
        <header>
            <h1>Ajout d'une préférence de film</h1>
        </header>
        <main>
            <form method="POST" name="editFavoriteMovie" action="editFavoriteMovie.php">
                <label>Titre :</label>
                <select name="filmID">
                    <?php
                    $films = $fctManager->getMoviesNonAlreadyMarkedAsFavorite($_SESSION['userID']);
                    // s'il y a des résultats
                    if ($films) {
                        foreach ($films as $film) {
                            ?>
                            <option value="<?= $film['filmID'] ?>"><?= $film['titre'] ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <div class="error">
                    <?php
                    if (!$aFilmIsSelected) {
                        echo "Veuillez renseigner un titre de film.";
                    }
                    ?>
                </div>
                <label>Commentaire :</label>
                <textarea name="comment"><?= $preference['commentaire'] ?></textarea>
                <br/>
                <input type="hidden" value="<?= $preference['userID'] ?>" name="userID"/>
                <div class="button-container">
                    <button type="submit" name="backToList">Retour à la liste</button>
                    <button type="submit" name="saveEntry">Sauvegarder</button>
                </div>
            </form>
        </main>
        <footer>
            <span class="copyleft">&copy;</span> 2025 Gestion de Cinéma. Tous droits inversés.
        </footer>
    </body>
</html>
