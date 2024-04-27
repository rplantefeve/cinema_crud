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
        if ($mode === "edit" && isset($toBeModified) === true && $film->getFilmId() === $toBeModified) {
            ?>
            <tr>
                <form name="editMovie" action="<?= $path . '/movie/save/' . $toBeModified ?>" method="POST">
                    <td><input name="titre" value="<?= $filmToBeModified->getTitre() ?>" /></td>
                    <td><input name="titreOriginal" value="<?= $filmToBeModified->getTitreOriginal() ?>" /></td>
                    <td colspan="3" class="centered">
                        <input name="filmID" type="hidden" value="<?= $toBeModified ?>" />
                        <input name="modificationInProgress" type="hidden" value="" />
                        <input type="image" src="<?= $path . '/images/cancelIcon.png' ?>" alt="Cancel" form="cancelForm" />
                        <input type="image" src="<?= $path . '/images/validateIcon.png' ?>" alt="Validate" />
                    </td>
                </form>
            </tr>
        <?php } else {
            ?>
            <tr>
                <td><?= $film->getTitre(); ?></td>
                <td><?= $film->getTitreOriginal(); ?></td>
                <td>
                    <form name="movieShowtimes" action="<?= $path . '/showtime/movie/' . $film->getFilmId() ?>" method="GET">
                        <input type="submit" value="Consulter les séances" />
                    </form>
                </td>
                <?php if ($isUserAdmin === true) : ?>
                    <td>
                        <form name="modifyMovie" action="<?= $path . '/movie/list/edit/' . $film->getFilmId() ?>" method="GET">
                            <input type="hidden" name="filmID" value="<?= $film->getFilmId() ?>" />
                            <input type="image" src="<?= $path . '/images/modifyIcon.png' ?>" alt="Modify" />
                        </form>
                    </td>
                    <td>
                        <form name="deleteMovie" action="<?= $path . '/movie/delete/' . $film->getFilmId() ?>" method="POST">
                            <?php
                            if (in_array($film->getFilmId(), $onAirFilms) === true) {
                                ?>
                                <input type="image" src="<?= $path . '/images/deleteIconDisabled.png' ?>" alt="Delete" disabled/>
                                <?php
                            } else {
                                ?>
                                <input type="image" src="<?= $path . '/images/deleteIcon.png' ?>" alt="Delete" />
                                <?php
                            }
                            ?>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
            <?php
        }
    }
    if ($isUserAdmin === true) :
        if (isset($mode) === true && $mode === "add") {
            ?>
            <tr>
                <form name="addMovie" action="<?= $path . '/movie/add' ?>" method="POST">
                    <td><input name="titre" type="text" placeholder="Titre" required /></td>
                    <td><input name="titreOriginal" type="text" placeholder="Titre original" /></td>
                    <td colspan="3" class="centered">
                        <input type="image" src="<?= $path . '/images/cancelIcon.png' ?>" alt="Cancel" form="cancelForm" />
                        <input type="image" src="<?= $path . '/images/addIcon.png' ?>" alt="Add" />
                    </td>
                </form>
            </tr>
            <?php
        } else {
            ?>
            <tr class="new">
                <td colspan="5">
                    <form name="addMovie" method="get" action="<?= $request->getBasePath() . '/movie/list/add' ?>">
                        <button class="add" type="submit">Cliquer ici pour ajouter un film...</button>
                    </form>
                </td>
            </tr>
            <?php
        }
    endif; ?>
</table>
<form name="cancelForm" id="cancelForm" method="GET" action="<?= $path . '/movie/list' ?>">
</form>
<form name="backToMainPage" action="<?= $path . '/home' ?>">
    <input type="submit" value="Retour à l'accueil" />
</form>