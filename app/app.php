<?php

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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


// appels aux routes configurées
require dirname(__DIR__) . '/app/routes.php';
// Démarrage de l'application
$app->run();

