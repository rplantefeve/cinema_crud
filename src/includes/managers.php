<?php

use Semeformation\Mvc\Cinema_crud\includes\DBFunctions;
use Semeformation\Mvc\Cinema_crud\models\Utilisateur;
use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Semeformation\Mvc\Cinema_crud\models\Seance;
use Semeformation\Mvc\Cinema_crud\models\Prefere;
use Semeformation\Mvc\Cinema_crud\models\Film;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Création du logger
// Note : on pourrait différencier les loggers en fonction des objets manipulés
$logger = new Logger("Functions");
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . '/logs/functions.log'));

// instanciation des managers
$fctManager = new DBFunctions($logger);
$utilisateursMgr = new Utilisateur($logger);
$cinemasMgr = new Cinema($logger);
$seancesMgr = new Seance($logger);
$preferesMgr = new Prefere($logger);
$filmsMgr = new Film($logger);
