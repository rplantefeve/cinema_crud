<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/includes/functions.php';

// On utilise un gérant d'exceptions
set_error_handler("exception_error_handler");
// Création du logger
// Note : on pourrait différencier les loggers en fonction des objets manipulés
$logger = new Logger("App");
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/application.log'));

