# 🎥 cinema_crud

> Apprentissage du modèle MVC en PHP.

## 📖 Table des matières
- [🎥 cinema\_crud](#-cinema_crud)
  - [📖 Table des matières](#-table-des-matières)
  - [📚 Prérequis](#-prérequis)
  - [🚀 Déploiement](#-déploiement)
  - [📦 Installation](#-installation)
  - [⚙️ Configuration](#️-configuration)
  - [📝 Release notes](#-release-notes)
    - [Itération 02](#itération-02)
    - [Itération 01](#itération-01)


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

Pour configurer l'accès à la base de données, il faut configurer le fichier `src\includes\DBFactory.php` : `$user`, `$pass` et `$dataSourceName` (`host=` et `dbname=`).


## 📝 Release notes

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