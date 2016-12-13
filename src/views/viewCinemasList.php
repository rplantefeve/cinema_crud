<?php $this->titre = "Cinémas"; ?>
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
                <form name="cinemaShowtimes" action="<?= $request->getBasePath() . '/showtime/' . $cinema->getCinemaId() ?>" method="GET">
                    <input type="submit" value="Consulter les séances"/>
                </form>
            </td>
            <?php
            if ($isUserAdmin):
                ?>
                <td>
                    <form name="modifyCinema" action="<?= $request->getBasePath() . '/cinema/edit/' . $cinema->getCinemaId() ?>" method="GET">
                        <input type="submit" id="modify" value="" />
                    </form>
                </td>
                <td>
                    <form name="deleteCinema" action="<?= $request->getBasePath() . '/cinema/delete/' . $cinema->getCinemaId() ?>" method="POST">
                        <input type="image" src="<?= $request->getBasePath() . '/images/deleteIcon.png' ?>" alt="Delete"/>
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
                <form name="addCinema" method="get" action="<?= $request->getBasePath() . '/cinema/add' ?>">
                    <button class="add" type="submit">Cliquer ici pour ajouter un cinéma</button>
                </form>
            </td>
        </tr>
    <?php endif; ?>
</table>
<form name="backToMainPage" action="<?= $request->getBasePath() . '/home' ?>">
    <input type="submit" value="Retour à l'accueil"/>
</form>