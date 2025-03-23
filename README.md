# 🎥 cinema_crud

> Apprentissage du modèle MVC en PHP.

## 📖 Table des matières

- [📚 Prérequis](#-prérequis)
- [🚀 Déploiement](#-déploiement)
- [📦 Installation](#-installation)
- [⚙️ Configuration](#️-configuration)
- [📝 Release notes](#-release-notes)
    - [🌟 Itération fmk-03](#-itération-fmk-03)
    - [🌟 Itération fmk-02](#-itération-fmk-02)
    - [🌟 Itération fmk-01](#-itération-fmk-01)
        - [🛤️ Routes](#️-routes)
    - [Itération MVC-05](#itération-mvc-05)
        - [Liste des actions possibles](#liste-des-actions-possibles)
    - [Itération MVC-04](#itération-mvc-04)
    - [Itération MVC-03](#itération-mvc-03)
        - [Récapitulatif des modifications](#récapitulatif-des-modifications)
    - [Itération MVC-02](#itération-mvc-02)
    - [Itération MVC-01](#itération-mvc-01)
    - [Itération 03](#itération-03)
        - [Tableau récapitulatif des opérations CRUD](#tableau-récapitulatif-des-opérations-crud)
    - [Itération 02](#itération-02)
        - [Tableau récapitulatif des opérations CRUD](#tableau-récapitulatif-des-opérations-crud-1)
    - [Itération 01](#itération-01)
        - [Tableau récapitulatif des opérations CRUD](#tableau-récapitulatif-des-opérations-crud-2)

## 📚 Prérequis

- PHP 7.4
- Composer 2.2
- Serveur Apache2
- Base de données MySQL ou MariaDB

## 🚀 Déploiement

Pour déployer l'application, un script `scripts/first_deploy.sh` est disponible. Il permet de déployer l'application sur un serveur Apache2. Le script est découpé en sept étapes :
1. 📄 Création du fichier de configuration de site pour Apache2 dans le répertoire `/etc/apache2/sites-available/`.
2. 🗄️ Création de la base de données.
3. 👤 Création de l'utilisateur de la base de données.
4. 🛠️ Ajout de la résolution de l'hôte local dans le fichier `/etc/hosts`.
5. ✅ Activation du site.
6. 🔄 Redémarrage du serveur Apache2.
7. 🔒 Réinitialisation des permissions des fichiers.

## 📦 Installation

Éxécutez la commande `composer install` pour installer les dépendances.

**⚠️ Attention !** Ne pas exécuter la commande `composer update`, car cela pourrait mettre à jour les dépendances et casser l'application.

## ⚙️ Configuration

Pour configurer l'accès à la base de données, il n'y a rien à faire. Simplement, vérifier que les informations de connexion à la base de données sont correctes dans le fichier `app/config/prod.php`.

## 📝 Release notes

### 🌟 Itération fmk-03

La dernière itération a pour objectif d'utiliser un moteur de template pour gérer les vues de l'application (Twig).

- Toutes les vues sont migrées vers Twig.

### 🌟 Itération fmk-02

- 🛠️ Configurations de connexion à la base de données dans des fichiers de configuration `dev.php` et `prod.php`.
- 🗄️ Utilisation de Doctrine DBAL pour gérer les requêtes SQL.
- 🔄 Implémentation des méthodes `find` et `findAll` dans les DAO, mais aussi de `update` et `insert`.
- 💾 Les objets métiers sont hydratés par les données de la base de données et ensuite sauvegardés.

### 🌟 Itération fmk-01

Cette itération a pour objet d'utiliser un micro-framework applicatif pour gérer différents aspects de l'application, notamment le routage des requêtes et la gestion des vues.

- 📂 Restructuration des éléments Web dans `/web`.
- ✏️ Ré-écriture des URL pour les rendre plus lisibles dans un fichier `.htaccess`.
- 📩 Utilisation de la `Request` et de la `Response` pour gérer les requêtes et les réponses dans les contrôleurs.
- 🔗 Injection de la requête dans les vues.
- 🔄 Transformation des actions en routes.
- 🖋️ Actions des formulaires mises à jour pour appeler les routes.
- 🎨 Liens CSS mis à jour.
- 🗂️ Consignation reconfigurée.
- 🔐 Sessions gérées par le framework.
- 🛠️ Routage des requêtes géré par le framework.

#### 🛤️ Routes

| 🛠️ Ancienne action       | 🚀 Nouvelle route                          |
|--------------------------|------------------------------------------|
| Ø                        | /                                        |
| /home                    | /home                                   |
| /login                   | /login                                  |
| cinemasList              | /cinema/list                            |
| editCinema               | /cinema/edit/{cinemaId}                 |
|                          | /cinema/add                             |
| deleteCinema             | /cinema/delete/{cinemaId}               |
| moviesList               | /movie/list                             |
| editMovie                | /movie/edit/{filmId}                    |
|                          | /movie/add                              |
| deleteMovie              | /movie/delete/{filmId}                  |
| movieShowtimes           | /showtime/movie/{filmId}                |
| cinemaShowtimes          | /showtime/cinema/{cinemaId}             |
| editshowtime             | /showtime/edit/{filmId}/{cinemaId}      |
|                          | /showtime/movie/add/{filmId}            |
|                          | /showtime/cinema/add/{cinemaId}         |
|                          | /showtime/add/{filmId}/{cinemaId}       |
| deleteShowtime           | /showtime/delete/{filmId}/{cinemaId}    |
| editFavoriteMoviesList   | /favorite/list                          |
| editFavoriteMovie        | /favorite/edit/{userId}/{filmId}        |
|                          | /favorite/add                           |
| deleteFavoriteMovie      | /favorite/delete/{userId}/{filmId}      |
| createNewUser            | /user/add                               |
| logout                   | /logout                                 |


### Itération MVC-05

Cette itération a pour objectif d'utiliser des POPO (Plain Old PHP Object) pour représenter les objets métier.
De plus, les accès à la base de données sont désormais gérés par des classes DAO (Data Access Object).

- Création des classes DAO pour les objets métier
**Exemple :** La classe `Cinema` est désormais représentée par la classe `CinemaDAO` pour l'accès à la base de données et par la classe `Cinema` pour la représentation de l'objet métier.
- Création des classes POPO pour les objets métier
- les DAO deviennent des composants des contrôleurs
- toutes les vues font désormais appel aux getters des objets métier pour afficher les données

#### Bilan de toutes les itérations jusqu'à présent

Nous avons :
- Séparé la logique d’interface (vues) de la logique métier (modèle)
- Créé autant de vues qu’il y a d’écrans dans l’application
- Créé des objets métiers conteneurs des données des tables de la BDD
- Implémenté des DAO qui se chargent de créer ces objets métiers
Ce qu’il reste à faire :
- Utiliser des modèles (templates) de vues
- Utiliser un framework applicatif
- Utiliser un ORM

#### Liste des actions possibles 
| Action                  | Traitement afférent                                                                 |
|-------------------------|-------------------------------------------------------------------------------------|
| Ø                       | Afficher la page d'accueil                                                         |
| cinemasList             | Afficher la liste des cinémas                                                      |
| editCinema              | Ajouter/Modifier un cinéma                                                         |
| deleteCinema            | Supprimer un cinéma                                                                |
| moviesList              | Afficher la liste des films                                                        |
| editMovie               | Ajouter / Modifier un film                                                         |
| deleteMovie             | Supprimer un film                                                                  |
| movieShowtimes          | Afficher la liste des séances d’un film donné dans les différents cinémas          |
| cinemaShowtimes         | Afficher la liste des séances de films pour un cinéma donné                        |
| editshowtime            | Ajouter / Modifier une séance                                                      |
| deleteShowtime          | Supprimer une séance                                                               |
| editFavoriteMoviesList  | Modifier ses préférences de films                                                  |
| editFavoriteMovie       | Modifier une préférence en particulier                                             |
| deleteFavoriteMovie     | Supprimer une préférence de film                                                   |
| createNewUser           | Créer un nouvel utilisateur                                                        |
| logout                  | Se déconnecter                                                                     |



### Itération MVC-04

- Factorisation des éléments communs aux vues dans un fichier `views/viewTemplate.php`
- Modifcation de la classe `View` pour prendre en compte le template

### Itération MVC-03

- Passage en orienté objet
    - création de la classe `View` pour gérer les vues
    - création de contrôleurs dédiées
        - `HomeController` pour gérer les actions de la page d'accueil et les utilisateurs
        - `CinemaController` pour gérer les actions liées aux cinémas
        - `MovieController` pour gérer les actions liées aux films
        - `ShowtimesController` pour gérer les actions liées aux séances
        - `FavoriteController` pour gérer les actions liées aux films préférés
    - `DBFunctions.php` renommée en `Model.php`
    - création de la classe `Router` pour gérer les routes

#### Récapitulatif des modifications

Notre application est dorénavant conforme à l’architecture orientée objet. Nous avons :
- Une classe Routeur qui se charge de router les requêtes vers les contrôleurs dédiés
- Une classe View qui se charge de générer les vues
- Des classes modèles qui regroupent les logiques métier
- Des classes contrôleurs serviteurs qui manipulent les modèles


### Itération MVC-02

- `index.php` agit désormais comme contrôleur frontal, chargé de dispatcher les actions en fonction de la route demandée.
- `controllers/controleur.php` joue le rôle de contrôleur serviteur.

### Itération MVC-01

- Les fonctions de manipulation des objets métier ont été regroupées dans des classes dédiées situées dans le répertoire `models/`. 
  **Exemple :** Les fonctions `getCinemasList` et `getCinemaInformationsById` sont maintenant intégrées à la classe `Cinema`.
- Les parties HTML ont été extraites et isolées dans des fichiers du répertoire `views/`, nommés sous la forme `viewXXX.php`.
  **Exemple :** La partie vue de `cinemasList.php` est maintenant dans le fichier `viewCinemaList.php`.

### Itération 03

#### Gestion des cinémas

- Ajouter/Modifier/Supprimer un cinéma
- Ajouter/Modifier/Supprimer un film
- Ajouter/Modifier/Supprimer une séance pour un film et un cinéma donnés

#### Tableau récapitulatif des opérations CRUD

| Objet métier       | Create | Read | Update | Delete |
|--------------------|--------|------|--------|--------|
| **Utilisateur**    | ✅     | ❌   | ❌     | ❌     |
| **Préférence**     | ✅     | ✅   | ✅     | ✅     |
| **Cinéma**         | ✅     | ✅   | ✅     | ✅     |
| **Film**           | ✅     | ✅   | ✅     | ✅     |
| **Séance (Film)**  | ✅     | ✅   | ✅     | ✅     |
| **Séance (Cinéma)**| ✅     | ✅   | ✅     | ✅     |

### Itération 02

#### Espace utilisateur

- Modification (mise à jour / suppression) d'une préférence de film pour un utilisateur donné

#### Gestion des cinémas
- Consulter la liste des cinémas
- Consulter la liste des films
- Consulter la liste des séances pour un film donné
- Consulter la liste des séances pour un cinéma donné

#### Tableau récapitulatif des opérations CRUD

| Objet métier       | Create | Read | Update | Delete |
|--------------------|--------|------|--------|--------|
| **Utilisateur**    | ✅     | ❌   | ❌     | ❌     |
| **Préférence**     | ✅     | ✅   | ✅     | ✅     |
| **Cinéma**         | ❌     | ✅   | ❌     | ❌     |
| **Film**           | ❌     | ✅   | ❌     | ❌     |
| **Séance (Film)**  | ❌     | ✅   | ❌     | ❌     |
| **Séance (Cinéma)**| ❌     | ✅   | ❌     | ❌     |

✅ : Fonctionnalité implémentée  
❌ : Fonctionnalité non implémentée


### Itération 01

#### Espace utilisateur

- Inscription d'un utilisateur
- Authentification d'un utilisateur (login)
- Déconnexion d’un utilisateur (logout)
- Création d'une liste de films préférés pour un utilisateur donné

#### Tableau récapitulatif des opérations CRUD

| Objet métier       | Create | Read | Update | Delete |
|--------------------|--------|------|--------|--------|
| **Utilisateur**    | ✅     | ❌   | ❌     | ❌     |
| **Préférence**     | ✅     | ✅   | ❌     | ❌     |
| **Cinéma**         | ❌     | ❌   | ❌     | ❌     |
| **Film**           | ❌     | ❌   | ❌     | ❌     |
| **Séance (Film)**  | ❌     | ❌   | ❌     | ❌     |
| **Séance (Cinéma)**| ❌     | ❌   | ❌     | ❌     |