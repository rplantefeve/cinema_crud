<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

// si la méthode de formulaire est la méthode POST
if (filter_input(
    INPUT_SERVER,
    'REQUEST_METHOD'
) === "POST"
) {
    // on "sainifie" les entrées
    $sanitizedEntries = filter_input_array(
        INPUT_POST,
        [
            'userID' => FILTER_SANITIZE_NUMBER_INT,
            'filmID' => FILTER_SANITIZE_NUMBER_INT,
        ]
    );

    // suppression de la préférence de film
    $preferesMgr->deleteFavoriteMovie(
        $sanitizedEntries['userID'],
        $sanitizedEntries['filmID']
    );
}
// redirection vers la liste des préférences de films
header("Location: editFavoriteMoviesList.php");
exit;
