<?php

namespace Semeformation\Mvc\Cinema_crud;

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Création du logger
// Note : on pourrait différencier les loggers en fonction des objets manipulés
$logger = new Logger("App");
$logger->pushHandler(new StreamHandler(__DIR__ . './logs/application.log'));

// Initialisation de l'application Silex
$app          = new Application();
// Mode debug ON
$app['debug'] = true;
// Enregistrement du gestionnaire de sessions
$app->register(new SessionServiceProvider());

// appels aux routes configurées
require dirname(__DIR__) . '/app/routes.php';
// Démarrage de l'application
$app->run();
