<?php 
$this->titre = "Ajouter / Modifier un film"; 
$path = $request->getBasePath();
// si c'est une modification
if ($film && $film->getFilmId()) {
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
    <input type="submit" name="saveEntry" value="Sauvegarder"/>
</form>
<form method="get" action="<?= $path . '/movie/list' ?>">
    <input type="submit" value="Retour à la liste"/>
</form>