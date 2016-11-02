<?php

use Semeformation\Mvc\Cinema_crud\includes\DBFunctions;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Création du logger
$logger = new Logger("Functions");
$logger->pushHandler(new StreamHandler(dirname(__DIR__) . './logs/functions.log'));
// Manager de connexion à la BDD
$manager = new DBFunctions($logger);

