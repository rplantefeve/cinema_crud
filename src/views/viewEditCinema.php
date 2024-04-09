<?php
$this->title = "Ajouter / Modifier un cinéma";
$path        = $request->getBasePath();
// si c'est une modification
if (!$isItACreation) {
    $action = $path . '/cinema/edit/' . $cinema->getCinemaId();
} else {
    $action = $path . '/cinema/add';
}
?>
<h1>Ajouter/Modifier un cinéma</h1>
<form method="POST" name="editCinema" action="<?= $action ?>">
    <label>Dénomination :</label>
    <input name="denomination" type="text" value="<?php
    if ($cinema) {
        echo $cinema->getDenomination();
    }
    ?>" required/>
    <label>Adresse :</label>
    <textarea name="adresse" required><?php
        if ($cinema) {
            echo $cinema->getAdresse();
        }
        ?></textarea>
    <br/>
    <input type="hidden" value="<?php
    if ($cinema) {
        echo $cinema->getCinemaId();
    }
    ?>" name="cinemaID"/>
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
        echo ' formaction = "' . $path . '/cinema/add"';
    }
    ?>/>
</form>
<form method="get" action="<?= $path . '/cinema/list' ?>">
    <input type="submit" value="Retour à la liste"/>
</form>