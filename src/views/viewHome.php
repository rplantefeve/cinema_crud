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
            <?php
            // si pas encore authentifié
            if (!$loginSuccess) :
                ?>
                <form method="POST" name="editFavoriteMoviesList" action="index.php">

                    <label>Adresse email : </label>
                    <input type="email" name="email" required/>
                    <label>Mot de passe  : </label>
                    <input type="password" name="password" required/>
                    <div class="error">
                        <?php
                        if (!$areCredentialsOK) :
                            echo "Les informations de connexions ne sont pas correctes.";
                        endif;
                        ?>
                    </div>
                    <input type="submit" value="Editer ma liste de films préférés"/>
                </form>
                <p>Pas encore d'espace personnel ? <a href="createNewUser.php">Créer sa liste de films préférés.</a></p>
                <?php
                // sinon (utilisateur authentifié)
            else :
                ?>
                <form action="editFavoriteMoviesList.php">
                    <input type="submit" value="Editer ma liste de films préférés"/>
                </form>
                <a href="logout.php">Se déconnecter</a>
            <?php endif; ?>
        </div>
        <!-- Gestion des cinémas -->
        <div>
            <header>
                <h1>Gestion des cinémas</h1>
                <form name="cinemasList" action="cinemasList.php">
                    <input type="submit" value="Consulter la liste des cinémas"/>
                </form>
                <form name="moviesList" action="moviesList.php">
                    <input type="submit" value="Consulter la liste des films"/>
                </form>
            </header>
        </div>
    </body>
</html>
