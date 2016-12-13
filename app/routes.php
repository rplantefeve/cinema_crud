<?php

/*
 * Routes HomeController 
 */
// Home page
$app->get('/',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\HomeController::home');

$app->get('/home',
                'Semeformation\\Mvc\\Cinema_crud\\controllers\\HomeController::home')
        ->bind('home');
// Se connecter
$app->post('/login',
                'Semeformation\\Mvc\\Cinema_crud\\controllers\\HomeController::home')
        ->bind('login');
// Se déconnecter
$app->get('/logout',
                'Semeformation\\Mvc\\Cinema_crud\\controllers\\HomeController::logout')
        ->bind('logout');
// Créer un utilisateur
$app->match('/user/add',
                'Semeformation\\Mvc\\Cinema_crud\\controllers\\HomeController::createNewUser')
        ->bind('user_add');

/*
 * Route FavoriteController
 */
// Ajouter / Modifier des préférences de films
$app->match('/favorite/list',
                'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::editFavoriteMoviesList')
        ->bind('favorite_list');

$app->match('/favorite/add', 'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::editFavoriteMovie')
        ->bind('favorite_add');

// Modifier une préférence de film
$app->match('/favorite/edit/{userId}/{filmId}', 'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::editFavoriteMovie'
)->bind('favorite_edit');

// Supprimer une préférence de film
$app->post('/favorite/delete/{userId}/{filmId}', 'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::deleteFavoriteMovie'
)->bind('favorite_delete');
