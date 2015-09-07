<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . './includes/managers.php';

// on récupère la liste des films ainsi que leurs informations
$films = $filmsMgr->getMoviesList();

// on inclut la vue correspondante
include __DIR__ . './views/viewMoviesList.php';
