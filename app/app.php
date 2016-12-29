<?php

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Enregistre les gérants des erreurs et exceptions
ErrorHandler::register();
ExceptionHandler::register();

// Création du logger
// Note : on pourrait différencier les loggers en fonction des objets manipulés
$logger = new Logger("App");
$logger->pushHandler(new StreamHandler(__DIR__ . './logs/application.log'));
// Initialisation de l'application Silex
$app    = new Application();

require __DIR__ . '/config/dev.php';

// Enregistrement du gestionnaire de sessions
$app->register(new SessionServiceProvider());
// Enregistrement du DBAL => crée automatiquement le service accessible par $app['db']
$app->register(new DoctrineServiceProvider());

// enregistrement du CinemaDAO
$app['dao.cinema'] = function () use ($app) {
    return new \Semeformation\Mvc\Cinema_crud\dao\CinemaDAO($app['db']);
};
// enregistrement du UtilisateurDAO
$app['dao.utilisateur'] = function () use ($app) {
    return new \Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO($app['db']);
};
// enregistrement du FilmDAO
$app['dao.film'] = function () use ($app) {
    return new \Semeformation\Mvc\Cinema_crud\dao\FilmDAO($app['db']);
};

// enregistrement du PrefereDAO
$app['dao.prefere'] = function () use ($app) {
    $prefereDAO = new \Semeformation\Mvc\Cinema_crud\dao\PrefereDAO($app['db']);
    // init. du FilmDAO
    $prefereDAO->setFilmDAO($app['dao.film']);
    // init. du UtilisateurDAO
    $prefereDAO->setUtilisateurDAO($app['dao.utilisateur']);
    return $prefereDAO;
};

// enregistrement du SeanceDAO
$app['dao.seance'] = function () use ($app) {
    $seanceDAO = new \Semeformation\Mvc\Cinema_crud\dao\SeanceDAO($app['db']);
    // init. du FilmDAO
    $seanceDAO->setFilmDAO($app['dao.film']);
    // init. du CinemaDAO
    $seanceDAO->setCinemaDAO($app['dao.cinema']);
    return $seanceDAO;
};


// appels aux routes configurées
require dirname(__DIR__) . '/app/routes.php';
// Démarrage de l'application
$app->run();

