<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . './includes/fctManager.php';

session_start();
// si l'utilisateur n'est pas connecté
if (!array_key_exists("user",
                $_SESSION)) {
    // renvoi à la page d'accueil
    header('Location: index.php');
    exit;
}

// variable de contrôle de formulaire
$aFilmIsSelected = true;
// variable qui sert à conditionner l'affichage du formulaire
$isItACreation = false;

// si la méthode de formulaire est la méthode POST
if (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "POST") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_POST,
            ['backToList' => FILTER_DEFAULT,
        'filmID' => FILTER_SANITIZE_NUMBER_INT,
        'userID' => FILTER_SANITIZE_NUMBER_INT,
        'comment' => FILTER_SANITIZE_STRING,
        'modificationInProgress' => FILTER_SANITIZE_STRING]);

    // si l'action demandée est retour en arrière
    if ($sanitizedEntries['backToList'] !== NULL) {
        // on redirige vers la page d'édition des films favoris
        header('Location: editFavoriteMoviesList.php');
        exit;
    }
    // sinon (l'action demandée est la sauvegarde d'un favori)
    else {
        // si un film a été selectionné 
        if ($sanitizedEntries['filmID'] !== NULL) {

            // et que nous ne sommes pas en train de modifier une préférence
            if ($sanitizedEntries['modificationInProgress'] == NULL) {
                // on ajoute la préférence de l'utilisateur
                $fctManager->insertNewFavoriteMovie($sanitizedEntries['userID'],
                        $sanitizedEntries['filmID'],
                        $sanitizedEntries['comment']);
            }
            // sinon, nous sommes dans le cas d'une modification
            else {
                // mise à jour de la préférence
                $fctManager->updateFavoriteMovie($sanitizedEntries['userID'],
                        $sanitizedEntries['filmID'],
                        $sanitizedEntries['comment']);
            }
            // on revient à la liste des préférences
            header('Location: editFavoriteMoviesList.php');
            exit;
        }
        // sinon (un film n'a pas été sélectionné)
        else {
            // 
            $aFilmIsSelected = false;
            $isItACreation = true;
            // initialisation des champs du formulaire
            $preference = [
                "userID" => $sanitizedEntries["userID"],
                "filmID" => "",
                "titre" => "",
                "commentaire" => $sanitizedEntries["comment"]];
            $userID = $sanitizedEntries['userID'];
        }
    }
// sinon (nous sommes en GET) et que l'id du film et l'id du user sont bien renseignés
} elseif (filter_input(INPUT_SERVER,
                'REQUEST_METHOD') === "GET") {

    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['filmID' => FILTER_SANITIZE_NUMBER_INT,
        'userID' => FILTER_SANITIZE_NUMBER_INT]);

    if ($sanitizedEntries && $sanitizedEntries['filmID'] !== NULL && $sanitizedEntries['filmID'] !== '' && $sanitizedEntries['userID'] !== NULL && $sanitizedEntries['userID'] !== '') {
        // on récupère les informations manquantes (le commentaire afférent)
        $preference = $fctManager->getFavoriteMovieInformations($sanitizedEntries['userID'],
                $sanitizedEntries['filmID']);
        // sinon, c'est une création
    } else {
        // C'est une création
        $isItACreation = true;
        // on initialise les autres variables de formulaire à vide
        $preference = [
            "userID" => $_SESSION['userID'],
            "filmID" => "",
            "titre" => "",
            "commentaire" => ""];
    }
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
        <form method="POST" name="editFavoriteMovie" action="editFavoriteMovie.php">
            <label>Titre :</label>
            <select name="filmID" <?php
            if (!$isItACreation): echo "disabled";
            endif;
            ?>>
                        <?php
                        // si c'est une création, on crée la liste des films dynamiquement
                        if ($isItACreation) {
                            $films = $fctManager->getMoviesNonAlreadyMarkedAsFavorite($_SESSION['userID']);
                            // s'il y a des résultats
                            if ($films) {
                                foreach ($films as $film) {
                                    ?>
                            <option value="<?= $film['filmID'] ?>"><?= $film['titre'] ?></option>
                            <?php
                        }
                    }
                }
                // sinon, c'est une modification, nous n'avons qu'une seule option dans la liste
                else {
                    ?>
                    <option selected="selected" value="<?= $preference['filmID'] ?>"><?= $preference['titre'] ?></option>
                    <?php
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
            <?php
            // si c'est une modification, c'est une information dont nous avons besoin
            if (!$isItACreation) {
                ?>
                <input type="hidden" name="modificationInProgress" value="true"/>
                <input type="hidden" name="filmID" value="<?= $preference['filmID'] ?>"/>
                <?php
            }
            ?>
            <input type="submit" name="saveEntry" value="Sauvegarder"/>
            <input type="submit" name="backToList" value="Retour à la liste"/>
        </form>
    </body>
</html>
