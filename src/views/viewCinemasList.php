<?php
$this->titre = "Cinémas";
$path        = $request->getBasePath();
?>
<header><h1>Liste des cinémas</h1></header>
<table class="std">
    <tr>
        <th>Nom</th>
        <th>Adresse</th>
        <th colspan="3">Action</th>
    </tr>
    <?php
    // boucle de construction de la liste des cinémas
    foreach ($cinemas as $cinema) {
        if ($mode === "edit" && isset($toBeModified) === true && $cinema->getCinemaId() === $toBeModified) {
            ?>
            <tr>
                <form name="editCinema" action="<?= $path . '/cinema/save/' . $toBeModified ?>" method="POST">
                    <td><input name="denomination" value="<?= $cinemaToBeModified->getDenomination() ?>" /></td>
                    <td><textarea name="adresse"><?= $cinemaToBeModified->getAdresse() ?></textarea></td>
                    <td colspan="3" class="centered">
                        <input name="cinemaID" type="hidden" value="<?= $toBeModified ?>" />
                        <input name="modificationInProgress" type="hidden" value="" />
                        <input type="image" src="<?= $path . '/images/cancelIcon.png' ?>" alt="Cancel" form="cancelForm" />
                        <input type="image" src="<?= $path . '/images/validateIcon.png' ?>" alt="Add" />
                    </td>
                </form>
            </tr>
            <?php
        } else {
            ?>
            <tr>
                <td><?= $cinema->getDenomination(); ?></td>
                <td><?= $cinema->getAdresse(); ?></td>
                <td>
                    <form name="cinemaShowtimes" action="<?= $path . '/showtime/cinema/' . $cinema->getCinemaId() ?>" method="GET">
                        <input type="submit" value="Consulter les séances" />
                    </form>
                </td>
                <?php
                if ($isUserAdmin === true) :
                    ?>
                    <td>
                        <form name="modifyCinema" action="<?= $path . '/cinema/list/edit/' . $cinema->getCinemaId() ?>" method="GET">
                            <input type="image" src="<?= $path . '/images/modifyIcon.png' ?>" alt="Modify" />
                        </form>
                    </td>
                    <td>
                        <form name="deleteCinema" action="<?= $path . '/cinema/delete/' . $cinema->getCinemaId() ?>" method="POST">
                            <?php
                            if (in_array($cinema->getCinemaId(), $onAirCinemas) === true) {
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
                <form name="addCinema" action="<?= $path . '/cinema/add' ?>" method="POST">
                    <td>
                        <input name="denomination" placeholder="Dénomination" required />
                    </td>
                    <td>
                        <textarea name="adresse" placeholder="Renseignez l'adresse ici..." required></textarea>
                    </td>
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
                    <form name="addCinema" method="get" action="<?= $request->getBasePath() . '/cinema/list/add' ?>">
                        <button class="add" type="submit">Cliquer ici pour ajouter un cinéma</button>
                    </form>
                </td>
            </tr>

        <?php }
    endif; ?>
</table>
<form name="cancelForm" id="cancelForm" method="GET" action="<?= $path . '/cinema/list' ?>">
</form>
<form name="backToMainPage" action="<?= $path . '/home' ?>">
    <input type="submit" value="Retour à l'accueil" />
</form>