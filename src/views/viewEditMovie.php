<?php 
$this->titre = "Ajouter / Modifier un film"; 
$path = $request->getBasePath();
// si c'est une modification
if (!$isItACreation) {
    $action = $path . '/movie/edit/' . $film->getFilmId();
}
else {
    $action = $path . '/movie/add';
}
?>
<h1>Ajouter/Modifier un film</h1>
<form method="POST" name="editCinema" action="<?= $action ?>">
    <label>Titre :</label>
    <input name="titre" type="text" value="<?php
    if ($film): echo $film->getTitre();
    endif;
    ?>" required/>
    <label>Titre original :</label>
    <input name="titreOriginal" type="text" value="<?php
    if ($film): echo $film->getTitreOriginal();
    endif;
    ?>" />
    <br/>
    <input type="hidden" value="<?php
    if ($film) : echo $film->getFilmId();
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
    <input type="submit" name="saveEntry" value="Sauvegarder" 
<?php
    if ($isItACreation) {
        echo ' formaction = "' . $path . '/movie/add"';
    }
    ?>/>
</form>
<form method="get" action="<?= $path . '/movie/list' ?>">
    <input type="submit" value="Retour à la liste"/>
</form>