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

$app->match('/favorite/list/{addMode}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::editFavoriteMoviesList')
->bind('favorite_list_add');

// Modifier une préférence de film
$app->match('/favorite/list/{addMode}/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::editFavoriteMoviesList'
)->bind('favorite_list_edit');

$app->match('/favorite/add',
                'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::editFavoriteMovie')
->bind('favorite_add');

$app->post('/favorite/save/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::editFavoriteMovie'
)->bind('favorite_save');

// Supprimer une préférence de film
$app->post('/favorite/delete/{userId}/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\FavoriteController::deleteFavoriteMovie'
)->bind('favorite_delete');

/*
 * Routes CinemaController et MovieController 
 */
$app->get('/cinema/list',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\CinemaController::cinemasList')->bind('cinema_list');

$app->get('/movie/list',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\MovieController::moviesList')->bind('movie_list');

$app->match('/cinema/list/{addMode}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\CinemaController::cinemasList')
->bind('cinema_list_add');

$app->match('/movie/list/{addMode}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\MovieController::moviesList')
->bind('movie_list_add');

$app->match('/movie/list/{addMode}/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\MovieController::moviesList'
)->bind('movie_list_edit');

$app->match('/cinema/list/{addMode}/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\CinemaController::cinemasList'
)->bind('cinema_list_edit');

$app->match('/cinema/add',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\CinemaController::editCinema')->bind('cinema_add');

$app->match('/movie/add',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\MovieController::editMovie')->bind('movie_add');

$app->post('/cinema/save/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\CinemaController::editCinema'
)->bind('cinema_save');

$app->post('/movie/save/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\MovieController::editMovie'
)->bind('movie_save');

$app->post('/cinema/delete/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\CinemaController::deleteCinema')->bind('cinema_delete');

$app->post('/movie/delete/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\MovieController::deleteMovie')->bind('movie_delete');

/*
 * Routes ShowtimesController
 */
$app->get('/showtime/cinema/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\ShowtimesController::cinemaShowtimes')->bind('showtime_cinema_list');

$app->get('/showtime/movie/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\ShowtimesController::movieShowtimes')->bind('showtime_movie_list');

$app->post('/showtime/delete/{filmId}/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\ShowtimesController::deleteShowtime')->bind('showtime_delete');

$app->match('/showtime/edit/{filmId}/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\ShowtimesController::editShowtime')->bind('showtime_edit');

$app->get('/showtime/cinema/add/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\ShowtimesController::editShowtime')->bind('showtime_cinema_add');

$app->get('/showtime/movie/add/{filmId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\ShowtimesController::editShowtime')->bind('showtime_movie_add');

$app->match('/showtime/add/{filmId}/{cinemaId}',
        'Semeformation\\Mvc\\Cinema_crud\\controllers\\ShowtimesController::editShowtime')->bind('showtime_add');
