<?php $this->title = "Ajouter / Modifier un film"; ?>
<h1>Ajouter/Modifier un film</h1>
<form method="POST" name="editMovie" action="index.php?action=editMovie">
    <label>Titre :</label>
    <input name="titre" type="text" value="<?php
    if ($film !== null): echo $film->getTitre();
    endif;
    ?>" required/>
    <label>Titre original :</label>
    <input name="titreOriginal" type="text" value="<?php
    if ($film !== null): echo $film->getTitreOriginal();
    endif;
    ?>" />
    <br/>
    <input type="hidden" value="<?php
    if ($film !== null) : echo $film->getFilmId();
    endif;
    ?>" name="filmID"/>
           <?php
           // si c'est une modification, c'est une information dont nous avons besoin
           if (!$isItACreation) {
               ?>
        <input type="hidden" name="modificationInProgress" value="true"/>
        <?php
    }
    ?>
    <input type="submit" name="saveEntry" value="Sauvegarder"/>
</form>
<form action="index.php" method="get">
    <input type="hidden" value="moviesList" name="action">
    <input type="submit" name="backToList" value="Retour à la liste"/>
</form>