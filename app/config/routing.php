<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();
$routes->add('ciao',
        new Route('/ciao',
        [
    '_controller' => 'FormationCinemaCrudMainBundle:Ciao:ciao']));

return $routes;

