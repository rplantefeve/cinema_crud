<?php

namespace Semeformation\Mvc\Cinema_crud\includes;

use Semeformation\Mvc\Cinema_crud\includes\DBFactory;
use Semeformation\Mvc\Cinema_crud\includes\Utils;
use PDO;
use Exception;

/*
 * Classe d'interrogation de la BDD. C'est elle qui contient toutes les fonctions
 * de manipulation des données de la base.
 */
class DBFunctions {


    public function __construct() {
    }


    /**
     * Exécute une requête SQL
     *
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return PDOStatement Résultats de la requête
     */
    public function executeQuery($sql, $params = null) {
        // si pas de paramètres
        if ($params == null) {
            // exécution directe
            $resultat = DBFactory::getFactory()->getConnection()->query($sql);
        } else {
            // requête préparée
            $resultat = DBFactory::getFactory()->getConnection()->prepare($sql);
            $resultat->execute($params);
        }
        return $resultat;
    }

    /*
     * Méthode qui teste si l'utilisateur est bien présent dans la BDD
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @throw Exception si on ne trouve pas l'utilisateur en BDD
     */

    public function verifyUserCredentials($email, $passwordSaisi) {
        // TODO
    }

    /*
     * Méthode qui teste si les passwords saisis sont identiques
     * @param string $passwordSaisi
     * @param string $passwordBDD
     * @param string $email Adresse email de l'utilisateur
     */

    private function testPasswords($passwordSaisi, $passwordBDD, $email) {
        // TODO
    }

    /*
     * Méthode qui retourne l'id d'un utilisateur passé en paramètre
     * @param string $utilisateur Adresse email de l'utilisateur
     * @return string $id Identifiant de l'utilisateur
     */

    public function getUserIDByEmailAddress($utilisateur) {
        // TODO
    }

    /*
     * Méthode qui retourne le nom et le prénom d'un utilisateur donné
     * @param string $utilisateur Adresse email de l'utilisateur
     * @return array[] Le nom et prénom de l'utilisateur
     */

    public function getCompleteUsernameByEmailAddress($utilisateur) {
        // on construit la requête qui va récupérer le nom et le prénom de l'utilisateur
        $requete = "SELECT userID, prenom, nom FROM utilisateur "
                . "WHERE adresseCourriel = :email";

        // on extrait le résultat de la BDD sous forme de tableau associatif
        $resultat = $this->extraire1xN($requete,
                ['email' => $utilisateur],
                false);

        // on retourne le résultat
        return $resultat;
    }

    /*
     * Méthode qui retourne les films préférés d'un utilisateur donné
     * @param string $utilisateur Adresse email de l'utilisateur
     * @return array[][] Les films préférés (sous forme de tableau associatif) de l'utilisateur
     */

    public function getFavoriteMoviesFromUser($id) {
        // TODO
    }

    /*
     * Méthode qui renvoie les informations sur un film favori donné pour un utilisateur donné
     * @param int $userID Identifiant de l'utilisateur
     * @param int $filmID Identifiant du film
     * @return array[]
     */

    public function getFavoriteMovieInformations($userID, $filmID) {
        // TODO
    }

    /*
     * Méthode qui ajoute un utilisateur dans la BDD
     * @param string $firstName Prénom de l'utilisateur
     * @param string $lastName Nom de l'utilisateur
     * @param string $email Adresse email de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     */

    public function createUser($firstName, $lastName, $email, $password) {
        // TODO
    }

    /*
     * Méthode qui ne renvoie que les titres et ID de films non encore marqués
     * comme favoris par l'utilisateur passé en paramètre
     * @param int $userID Identifiant de l'utilisateur
     * @return array[][] Titres et ID des films présents dans la base
     */

    public function getMoviesNonAlreadyMarkedAsFavorite($userID) {
        // TODO
    }

    /*
     * Méthode qui ajoute une préférence de film à un utilisateur
     * @param int userID Identifiant de l'utilisateur
     * @param int filmID Identifiant du film
     * @param string comment Commentaire de l'utilisateur à propos de ce film
     */

    public function insertNewFavoriteMovie($userID, $filmID, $comment = "") {
        // TODO
    }

    /*
     * Fonctions utilitaires
     */

    /**
     * Retourne les lignes d'enregistrements sous forme de tableau associatif
     * Ici, on aura N lignes, N colonnes
     * @param string $unSQLSelect La requête SQL
     * @param array $parametres Les éventuels paramètres de la requête
     * @param boolean $estVisible (visualisation du résultat)
     * @return array[][] ou null
     */
    private function extraireNxN($unSQLSelect, $parametres = null, $estVisible = false) {
        // tableau des résultats
        $tableau = array();
        // résultat de la requête
        $resultat = $this->executeQuery($unSQLSelect,
                $parametres);

        // boucle de construction du tableau de résultats
        while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
            $tableau[] = $ligne;
        }
        unset($resultat);

        // si la tableau ne contient pas d'élément
        if (count($tableau) == 0) {
            $tableau = null;
        }

        // si l'on souhaite afficher le contenu du tableau (DEBUG MODE)
        if ($estVisible) {
            Utils::afficherResultat($tableau,
                    $unSQLSelect);
        }

        // on retourne le tableau de résultats
        return $tableau;
    }

    /**
     * Retourne une ligne d'enregistrement sous forme de tableau associatif
     * @param string $unSQLSelect
     * @param array $parametres Tableau des paramètres de la requête
     * @param boolean $estVisible (visualisation du résultat)
     * @return array[] ou null
     */
    private function extraire1xN($unSQLSelect, $parametres = null, $estVisible = false) {
        $result = $this->extraireNxN($unSQLSelect,
                $parametres,
                false);
        if (isset($result[0])) {
            $result = $result[0];
        }
        if ($estVisible) {
            Utils::afficherResultat($result,
                    $unSQLSelect);
        }
        return $result;
    }

}
