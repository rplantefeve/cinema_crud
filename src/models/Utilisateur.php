<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\includes\DBFunctions;
use Exception;

class Utilisateur extends DBFunctions {
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

}
