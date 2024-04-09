<?php
$this->title = "Cinémas";
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
        ?>
        <tr>
            <td><?= $cinema->getDenomination(); ?></td>
            <td><?= $cinema->getAdresse(); ?></td>
            <td>
                <form name="cinemaShowtimes" action="<?= $path . '/showtime/cinema/' . $cinema->getCinemaId() ?>" method="GET">
                    <input type="submit" value="Consulter les séances"/>
                </form>
            </td>
            <?php
            if ($isUserAdmin):
                ?>
                <td>
                    <form name="modifyCinema" action="<?= $path . '/cinema/edit/' . $cinema->getCinemaId() ?>" method="GET">
                        <input type="submit" id="modify" value="" />
                    </form>
                </td>
                <td>
                    <form name="deleteCinema" action="<?= $path . '/cinema/delete/' . $cinema->getCinemaId() ?>" method="POST">
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
                <form name="addCinema" method="get" action="<?= $path . '/cinema/add' ?>">
                    <button class="add" type="submit">Cliquer ici pour ajouter un cinéma</button>
                </form>
            </td>
        </tr>
    <?php endif; ?>
</table>
<form name="backToMainPage" action="<?= $path . '/home' ?>">
    <input type="submit" value="Retour à l'accueil"/>
</form>