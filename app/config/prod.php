<?php

// Configuration Doctrine DBAL
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'cinema_crud',
    'user'     => 'userCinema',
    'password' => 'pwdCinema',
);
