<?php $this->title = "Cinémas"; ?>
<header>
    <h1>Liste des cinémas</h1>
</header>
<table class="std">
    <tr>
        <th>Nom</th>
        <th>Adresse</th>
        <th colspan="3">Action</th>
    </tr>
    <?php
    // boucle de construction de la liste des cinémas
    foreach ($cinemas as $cinema) {
    ?>
        <tr>
            <td><?= $cinema->getDenomination(); ?></td>
            <td><?= $cinema->getAdresse(); ?></td>
            <td>
                <form name="cinemaShowtimes" action="index.php" method="GET">
                    <input name="action" type="hidden" value="cinemaShowtimes" />
                    <input name="cinemaID" type="hidden" value="<?= $cinema->getCinemaId(); ?>" />
                    <input type="submit" value="Consulter les séances" />
                </form>
            </td>
            <?php
            if ($isUserAdmin) :
            ?>
                <td>
                    <form name="modifyCinema" action="index.php" method="GET">
                        <input name="action" type="hidden" value="editCinema">
                        <input type="hidden" name="cinemaID" value="<?= $cinema->getCinemaId() ?>" />
                        <input type="image" src="images/modifyIcon.png" alt="Modify" />
                    </form>
                </td>
                <td>
                    <form name="deleteCinema" action="index.php?action=deleteCinema" method="POST">
                        <input type="hidden" name="cinemaID" value="<?= $cinema->getCinemaId() ?>" />
                        <input type="image" src="images/deleteIcon.png" alt="Delete" />
                    </form>
                </td>
            <?php endif; ?>
        </tr>
        <?php
    }
    if ($isUserAdmin) :
        if (isset($mode) && $mode === "add") {
        ?>
            <tr>
                <form name="saveCinema" action="index.php?action=saveCinema" method="POST">
                    <td>
                        <input name="denomination" placeholder="Dénomination" />
                    </td>
                    <td>
                        <textarea name="adresse" placeholder="Renseignez l'adresse ici..."></textarea>
                    </td>
                    <td colspan="3" class="centered">
                        <input type="image" src="images/addIcon.png" alt="Add" />
                    </td>
                </form>
            </tr>
        <?php
        } else {
        ?>
            <tr class="new">
                <td colspan="5">
                    <form name="addCinema" method="get">
                        <input name="action" type="hidden" value="addCinema">
                        <button class="add" type="submit">Cliquer ici pour ajouter un cinéma</button>
                    </form>
                </td>
            </tr>

    <?php }
    endif; ?>
</table>
<form name="backToMainPage" action="index.php">
    <input type="submit" value="Retour à l'accueil" />
</form>