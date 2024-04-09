<?php $this->title = "Création d'un nouvel utilisateur"; ?>
<header><h1>Création d'un nouvel utilisateur</h1></header>
<form name="createUser" method="post" action="<?= $request->getBasePath() . '/user/add' ?>">
    <!-- la longueur maximum des input est en corrélation avec la longueur maximum des champs dans la BDD -->
    <label>Prénom :</label>
    <input name='firstName' type="text" maxlength="30" value="<?= $sanitizedEntries['firstName']
?>" />
    <div class="error">
        <?php
        if ($isFirstNameEmpty) {
            echo "Veuillez renseigner un prénom.";
        }
        ?>
    </div>
    <label>Nom :</label>
    <input name='lastName' type="text" maxlength="50" value="<?= $sanitizedEntries['lastName'] ?>" />
    <div class="error">
        <?php
        if ($isLastNameEmpty) {
            echo "Veuillez renseigner un nom.";
        }
        ?>
    </div>
    <label>Adresse email :</label>
    <input name='email' type="email" maxlength="90" value="<?= $sanitizedEntries['email'] ?>" />
    <div class="error">
        <?php
        if ($isEmailAddressEmpty) {
            echo "Veuillez renseigner une adresse email.";
        } else if (!$isUserUnique) {
            echo "Cet utilisateur existe déjà !";
        }
        ?>
    </div>
    <label>Mot de passe :</label>
    <input name='password' type="password"/>
    <div class="error">
        <?php
        if ($isPasswordEmpty) {
            echo "Veuillez rentrer un mot de passe.";
        }
        ?>
    </div>
    <label>Confirmation :</label>
    <input name='passwordConfirmation' type="password"/>
    <div class="error">
        <?php
        if ($isPasswordConfirmationEmpty) {
            echo "Veuillez confirmer le mot de passe.";
        } else if (!$isPasswordValid) {
            echo "Les mots de passe ne correspondent pas !";
        }
        ?>
    </div>
    <input type="submit" value="Créer un nouvel utilisateur"/>
</form>
<form name="backToMainPage" action="<?= $request->getBasePath() . '/home' ?>">
    <input type="submit" value="Retour à l'accueil"/>
</form>