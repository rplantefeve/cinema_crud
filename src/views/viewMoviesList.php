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
                <form name="editMovie" action="index.php?action=saveMovie" method="POST">
                    <td><input name="titre" value="<?= $filmToBeModified->getTitre() ?>" /></td>
                    <td><input name="titreOriginal" value="<?= $filmToBeModified->getTitreOriginal() ?>" /></td>
                    <td colspan="3" class="centered">
                        <input name="filmID" type="hidden" value="<?= $filmToBeModified->getFilmId() ?>" />
                        <input name="modificationInProgress" type="hidden" value="" />
                        <input type="image" src="images/cancelIcon.png" alt="Cancel" form="cancelForm" />
                        <input type="image" src="images/validateIcon.png" alt="Add" />
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
                        <form name="modifyMovie" action="index.php" method="GET">
                            <input name="action" type="hidden" value="editMovie">
                            <input type="hidden" name="filmID" value="<?= $film->getFilmId() ?>" />
                            <input type="image" src="images/modifyIcon.png" alt="Modify" />
                        </form>
                    </td>
                    <td>
                        <form name="deleteMovie" action="index.php?action=deleteMovie" method="POST">
                            <input type="hidden" name="filmID" value="<?= $film->getFilmId() ?>" />
                            <?php
                            if (in_array($film->getFilmId(), $onAirFilms) === true) {
                                ?>
                                <input type="image" src="images/deleteIconDisabled.png" alt="Delete" disabled/>
                                <?php
                            } else {
                                ?>
                                <input type="image" src="images/deleteIcon.png" alt="Delete" />
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
                <form name="saveMovie" action="index.php?action=saveMovie" method="POST">
                    <td><input name="titre" type="text" placeholder="Titre" required /></td>
                    <td><input name="titreOriginal" type="text" placeholder="Titre original" /></td>
                    <td colspan="3" class="centered">
                        <input type="image" src="images/cancelIcon.png" alt="Cancel" form="cancelForm" />
                        <input type="image" src="images/addIcon.png" alt="Add" />
                    </td>
                </form>
            </tr>
            <?php
        } else {
            ?>
            <tr class="new">
                <td colspan="5">
                    <form name="addMovie" action="index.php">
                        <input name="action" type="hidden" value="addMovie">
                        <button class="add" type="submit">Cliquer ici pour ajouter un film...</button>
                    </form>
                </td>
            </tr>
            <?php
        }
    endif; ?>
</table>
<form name="cancelForm" id="cancelForm" method="GET" action="<?= $path . '/movie/list' ?>">
    <input name="action" type="hidden" value="moviesList" />
</form>
<form name="backToMainPage" action="<?= $path . '/home' ?>">
    <input type="submit" value="Retour à l'accueil" />
</form>