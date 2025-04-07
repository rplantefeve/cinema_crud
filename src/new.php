<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue dans la gestion de cinéma</title>
    <link rel="stylesheet" href="css/cinema.css">
    <script src="js/main.js"></script>
</head>

<body>
    <header>
        <h1>Bienvenue dans l'application de gestion de cinéma</h1>
    </header>
    <main>
        <p>Gérez votre cinéma efficacement avec notre application. Vous pouvez ajouter, mettre à jour et supprimer des enregistrements de films, gérer les horaires des séances, et bien plus encore.</p>
        <div id="error" class="error"></div>
        <div id="info" class="info"></div>
        <?php if(isset($_SESSION['username'])): ?>
            <p>Vous êtes connecté en tant que <?= $_SESSION['username'] ?></p>
            <a href="logout.php">Se déconnecter</a>
        <?php else: ?>
        <form id="loginForm" method="post" action="">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Éditer ma liste de films préférés</button>
        </form>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2023 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
    <script>
        <?php if (isset($errorMessage)): ?>
            showMessage('error', '<?= $errorMessage ?>');
        <?php endif; ?>
        <?php if (isset($infoMessage)): ?>
            showMessage('info', '<?= $infoMessage ?>');
        <?php endif; ?>
    </script>
</body>

</html>