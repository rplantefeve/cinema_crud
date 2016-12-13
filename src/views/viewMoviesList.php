<?php 
$this->titre = 'Films';
$path        = $request->getBasePath();
?>
<header><h1>Liste des films</h1></header>
<table class="std">
    <tr>
        <th>Titre</th>
        <th>Titre original</th>
        <th colspan="3">Action</th>
    </tr>
    <?php
    // boucle de construction de la liste des cinémas
    foreach ($films as $film) {
        ?>
        <tr>
            <td><?= $film->getTitre(); ?></td>
            <td><?= $film->getTitreOriginal(); ?></td>
            <td>
                <form name="movieShowtimes" action="<?= $path . '/showtime/movie/' . $film->getFilmId() ?>" method="GET">
                    <input type="submit" value="Consulter les séances"/>
                </form>
            </td>
            <?php
            if ($isUserAdmin):
                ?>
                <td>
                    <form name="modifyMovie" action="<?= $path . '/movie/edit/' . $film->getFilmId() ?>" method="GET">
                        <input type="submit" id="modify" value="" />
                    </form>
                </td>
                <td>
                    <form name="deleteMovie" action="<?= $path . '/movie/delete/' . $film->getFilmId() ?>" method="POST">
                        <input type="image" src="<?= $path . '/images/deleteIcon.png' ?>" alt="Delete"/>
                    </form>
                </td>
            <?php endif; ?>
        </tr>
        <?php
    }
    if ($isUserAdmin):
    ?>
        <tr class="new">
            <td colspan="5">
                <form name="addMovie" action="<?= $path . '/movie/add' ?>">
                    <button class="add" type="submit">Cliquer ici pour ajouter un film...</button>
                </form>
            </td>
        </tr>
    <?php endif; ?>
</table>
<form name="backToMainPage" action="<?= $path . '/home' ?>">
    <input type="submit" value="Retour à l'accueil"/>
</form>