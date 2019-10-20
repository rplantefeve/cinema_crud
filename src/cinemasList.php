<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/includes/managers.php';

$isUserAdmin = false;

session_start();
// si l'utilisateur est pas connecté et qu'il est amdinistrateur
if (array_key_exists("user", $_SESSION) and $_SESSION['user'] == 'admin@adm.adm') {
    $isUserAdmin = true;
}

// on récupère la liste des cinémas ainsi que leurs informations
$cinemas = $cinemasMgr->getCinemasList();

// on inclut la vue correspondante
include __DIR__ . '/views/viewCinemasList.php';
