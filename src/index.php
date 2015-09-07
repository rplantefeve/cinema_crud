<?php

require_once __DIR__ . '/vendor/autoload.php';

// init. des managers
require_once __DIR__ . '/includes/managers.php';

// initialisation de l'application
require_once __DIR__ . '/init.php';

// appel au contrôleur serviteur
require __DIR__ . './controllers/controleur.php';

try {
// on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(INPUT_GET,
            ['action' => FILTER_SANITIZE_STRING]);
    if ($sanitizedEntries && $sanitizedEntries['action'] !== '') {
        // si l'action demandée est la liste des cinémas
        if ($sanitizedEntries['action'] == "cinemasList") {
            // Activation de la route cinemasList
            cinemasList($managers);
        } else {
            // Activation de la route par défaut (page d'accueil)
            home($managers);
        }
    } else {
        // Activation de la route par défaut (page d'accueil)
        home($managers);
    }
} catch (Exception $e) {
    error($e->getMessage());
}
