<?php

namespace Semeformation\Mvc\Cinema_crud\includes;

use Semeformation\Mvc\Cinema_crud\includes\DBFactory;
use Semeformation\Mvc\Cinema_crud\includes\Utils;
use Psr\Log\LoggerInterface;
use PDO;
use Exception;

class DBFunctions {

    // logger
    private $logger;

    public function __construct(LoggerInterface $logger = null) {
        $this->logger = $logger;
    }

    public function getLogger() {
        return $this->logger;
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
            $resultat = DBFactory::getFactory($this->logger)->getConnection()->query($sql);
        } else {
            // requête préparée
            $resultat = DBFactory::getFactory($this->logger)->getConnection()->prepare($sql);
            $resultat->execute($params);
        }
        if ($this->logger) {
            $this->logger->debug('Query successfully executed : ' . $sql);
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
        // extraction du mdp de l'utilisateur
        $requete = "SELECT password FROM utilisateur WHERE adresseCourriel = :email";
        // on prépare la requête
        $statement = $this->executeQuery($requete,
                ['email' => $email]);

        // on teste le nombre de lignes renvoyées
        if ($statement->rowCount() > 0) {
            // on récupère le mot de passe
            $passwordBDD = $statement->fetch()[0];
            $this->testPasswords($passwordSaisi,
                    $passwordBDD,
                    $email);
        } else {
            throw new Exception('The user ' . $email . ' doesn\'t exist.');
        }
    }

    /*
     * 
     */

    private function testPasswords($passwordSaisi, $passwordBDD, $email) {
        // on teste si les mots de passe correspondent
        if (password_verify($passwordSaisi,
                        $passwordBDD)) {
            if ($this->logger) {
                $this->logger->info('User ' . $email . ' now connected.');
            }
        } else {
            throw new Exception('Bad password for the user ' . $email);
        }
    }

    /*
     * Méthode qui retourne l'id d'un utilisateur passé en paramètre
     * @param string $utilisateur Adresse email de l'utilisateur
     * @return string $id Identifiant de l'utilisateur
     */

    public function getUserIDByEmailAddress($utilisateur) {
        // requête qui récupère l'ID grâce à l'adresse email
        $requete = "SELECT userID FROM utilisateur WHERE adresseCourriel = :email";

        // on récupère le résultat de la requête
        $resultat = $this->executeQuery($requete,
                ['email' => $utilisateur]);

        // on teste le nombre de lignes renvoyées
        if ($resultat->rowCount() > 0) {
            // on récupère la première (et seule) ligne retournée
            $row = $resultat->fetch();
            // l'id est le premier élément du tableau de résultats
            return $row[0];
        } else {
            return null;
        }
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
        // on construit la requête qui va récupérer les films de l'utilisateur
        $requete = "SELECT f.filmID, f.titre, p.commentaire from film f" .
                " INNER JOIN prefere p ON f.filmID = p.filmID" .
                " AND p.userID = " . $id;

        // on extrait le résultat de la BDD sous forme de tableau associatif
        $resultat = $this->extraireNxN($requete,
                null,
                false);

        // on retourne le résultat
        return $resultat;
    }

    /*
     * Méthode qui renvoie les informations sur un film favori donné pour un utilisateur donné
     * @param int $userID Identifiant de l'utilisateur
     * @param int $filmID Identifiant du film
     * @return array[]
     */

    public function getFavoriteMovieInformations($userID, $filmID) {
        // requête qui récupère les informations d'une préférence de film pour un utilisateur donné
        $requete = "SELECT f.titre, p.userID, p.filmID, p.commentaire"
                . " FROM prefere p INNER JOIN film f ON p.filmID = f.filmID"
                . " WHERE p.userID = "
                . $userID
                . " AND p.filmID = "
                . $filmID;

        // on extrait les résultats de la BDD
        $resultat = $this->extraire1xN($requete,
                null,
                false);
        // on retourne le résultat
        return $resultat;
    }

    /*
     * Méthode qui ajoute un utilisateur dans la BDD
     * @param string $firstName Prénom de l'utilisateur
     * @param string $lastName Nom de l'utilisateur
     * @param string $email Adresse email de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     */

    public function createUser($firstName, $lastName, $email, $password) {
        // construction de la requête
        $requete = "INSERT INTO utilisateur (prenom, nom, adresseCourriel, password) "
                . "VALUES (:firstName, :lastName, :email, :password)";

        // exécution de la requête
        $this->executeQuery($requete,
                [':firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'password' => $password]);

        if ($this->logger) {
            $this->logger->info('User ' . $email . ' successfully created.');
        }
    }

    /*
     * Méthode qui renvoie la liste des films
     * @return array[][]
     */

    public function getMoviesList() {
        $requete = "SELECT * FROM film";
        // on retourne le résultat
        return $this->extraireNxN($requete,
                        null,
                        false);
    }

    /*
     * Méthode qui ne renvoie que les titres et ID de films non encore marqués
     * comme favoris par l'utilisateur passé en paramètre
     * @param int $userID Identifiant de l'utilisateur
     * @return array[][] Titres et ID des films présents dans la base
     */

    public function getMoviesNonAlreadyMarkedAsFavorite($userID) {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été marqués comme favoris par l'utilisateur
        $requete = "SELECT f.filmID, f.titre "
                . "FROM film f"
                . " WHERE f.filmID NOT IN ("
                . "SELECT filmID"
                . " FROM prefere"
                . " WHERE userID = :id"
                . ")";
        // extraction de résultat
        $resultat = $this->extraireNxN($requete,
                ['id' => $userID],
                false);
        // retour du résultat
        return $resultat;
    }

    /*
     * Méthode qui ajoute une préférence de film à un utilisateur
     * @param int userID Identifiant de l'utilisateur
     * @param int filmID Identifiant du film
     * @param string comment Commentaire de l'utilisateur à propos de ce film
     */

    public function insertNewFavoriteMovie($userID, $filmID, $comment = "") {
        // on construit la requête d'insertion
        $requete = "INSERT INTO prefere (filmID, userID, commentaire) VALUES ("
                . ":filmID"
                . ", :userID"
                . ", :comment)";

        // exécution de la requête
        $this->executeQuery($requete,
                ['filmID' => $filmID,
            'userID' => $userID,
            'comment' => $comment]);

        if ($this->logger) {
            $this->logger->info('Movie ' . $filmID . ' successfully added to ' . $userID . '\'s preferences.');
        }
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