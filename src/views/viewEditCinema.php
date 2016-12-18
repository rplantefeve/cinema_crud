<?php
$this->titre = "Ajouter / Modifier un cinéma";
$path        = $request->getBasePath();
// si c'est une modification
if ($cinema && $cinema->getCinemaId()) {
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
    <input type="submit" name="saveEntry" value="Sauvegarder" />
</form>
<form method="get" action="<?= $path . '/cinema/list' ?>">
    <input type="submit" value="Retour à la liste"/>
</form>