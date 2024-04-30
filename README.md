cinema_crud
===========

Apprentissage MVC en PHP.

Configuration
----------------

Il est nécessaire de configurer l'accès à la BDD dans un fichier `/src/conf/parameters.ini` en prenant comme modèle `/src/conf/parameters.ini.dist`.
De plus, il faut exécuter les scripts situés dans le répertoire `/db`.

Itération MVC-02
----------------

* `index.php` est dorénavant le contrôleur frontal, celui qui dispatche les actions en fonction de la route demandée
* `controllers/controleur.php` constitue le contrôleur serviteur

Itération MVC-01
----------------

* Les fonctions de manipulations d'objets métier ont été regroupées dans des classes nommées à cet effet.
  
> Ex. : les fonctions ``getCinemasList`` et ``getCinemaInformationsById`` ont été regroupées dans une classe ``Cinema``.

* Les parties HTML ont été extraites et isolées dans des fichiers views/viewXXX.php
