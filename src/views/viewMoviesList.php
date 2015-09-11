<?php $this->titre = 'Films'; ?>
<header><h1>Liste des films</h1></header>
<table class="std">
    <tr>
        <th>Titre</th>
        <th>Titre original</th>
    </tr>
    <?php
    // boucle de construction de la liste des cinémas
    foreach ($films as $film) {
        ?>
        <tr>
            <td><?= $film->getTitre(); ?></td>
            <td><?= $film->getTitreOriginal(); ?></td>
            <td>
                <form name="movieShowtimes" action="index.php" method="GET">
                    <input name="action" type="hidden" value="movieShowtimes"/>
                    <input name="filmID" type="hidden" value="<?= $film->getFilmId(); ?>"/>
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
