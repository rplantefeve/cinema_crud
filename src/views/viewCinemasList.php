<?php $this->titre = "Cinémas"; ?>
<header><h1>Liste des cinémas</h1></header>
<table class="std">
    <tr>
        <th>Nom</th>
        <th>Adresse</th>
    </tr>
    <?php
    // boucle de construction de la liste des cinémas
    foreach ($cinemas as $cinema) {
        ?>
        <tr>
            <td><?= $cinema['DENOMINATION'] ?></td>
            <td><?= $cinema['ADRESSE'] ?></td>
            <td>
                <form name="cinemaShowtimes" action="index.php" method="GET">
                    <input name="action" type="hidden" value="cinemaShowtimes"/>
                    <input name="cinemaID" type="hidden" value="<?= $cinema['CINEMAID'] ?>"/>
                    <input type="submit" value="Consulter les séances"/>
                </form>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<form name="backToMainPage" action="index.php">
    <input type="submit" value="Retour à l'accueil"/>
</form>