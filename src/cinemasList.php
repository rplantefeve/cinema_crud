<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

// on récupère la liste des cinémas ainsi que leurs informations
$cinemas = $cinemasMgr->getCinemasList();

// on inclut la vue correspondante
include __DIR__ . '/views/viewCinemasList.php';
