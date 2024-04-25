<?php $this->titre = 'Accueil'; ?>
<div>
    <header>
        <h1>Espace personnel</h1>
    </header>
    <?php
    // si pas encore authentifié
    if ($loginSuccess === false) :
        ?>
        <form method="post" name="editFavoriteMoviesList" action="<?= $request->getBasePath() . '/login' ?>">

            <label>Adresse email : </label>
            <input type="email" name="email" required/>
            <label>Mot de passe  : </label>
            <input type="password" name="password" required/>
            <div class="error">
                <?php
                if ($areCredentialsOK === false) :
                    echo "Les informations de connexions ne sont pas correctes.";
                endif;
                ?>
            </div>
            <input type="submit" value="Editer ma liste de films préférés"/>
        </form>
        <p>Pas encore d'espace personnel ? <a href="<?= $request->getBasePath() . '/user/add' ?>">Créer sa liste de films préférés.</a></p>
        <?php
        // sinon (utilisateur authentifié)
    else :
        ?>
        <form action="<?= $request->getBasePath() . '/favorite/list' ?>">
            <input type="submit" value="Editer ma liste de films préférés"/>
        </form>
        <a href="<?= $request->getBasePath() . '/logout' ?>">Se déconnecter</a>
    <?php endif; ?>
</div>
<!-- Gestion des cinémas -->
<div>
    <header>
        <h1>Gestion des cinémas</h1>
        <form name="cinemasList" method="GET" action="<?= $request->getBasePath() . '/cinema/list' ?>">
            <input type="submit" value="Consulter la liste des cinémas"/>
        </form>
        <form name="moviesList" method="GET" action="<?= $request->getBasePath() . '/movie/list' ?>">
            <input type="submit" value="Consulter la liste des films"/>
        </form>
    </header>
</div>
