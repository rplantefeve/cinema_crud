<?php
require_once __DIR__ . '/vendor/autoload.php';

require __DIR__ . './includes/Manager.php';

// TODO, below it's just an incomplete example of calling the manager
$email = '';
$passwordSaisi = '';
$manager->verifyUserCredentials($email, $passwordSaisi);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Cinéma CRUD</title>
        <link type="text/css" href="css/cinema.css" rel="stylesheet"/>
    </head>
    <body>
        <div>
            <header>
                <h1>Espace personnel</h1>
            </header>
                <form method="POST" name="editFavoriteMoviesList" action="index.php">

                    <label>Adresse email : </label>
                    <input type="email" name="email" required/>
                    <label>Mot de passe  : </label>
                    <input type="password" name="password" required/>
                    <input type="submit" value="Editer ma liste de films préférés"/>
                </form>
                <p>Pas encore d'espace personnel ? <a href="createNewUser.php">Créer sa liste de films préférés.</a></p>
        </div>
    </body>
</html>
