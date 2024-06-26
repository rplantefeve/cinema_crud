<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Utilisateur;
use Semeformation\Mvc\Cinema_crud\exceptions\BusinessObjectDoNotExist;
use Exception;
use Semeformation\Mvc\Cinema_crud\exceptions\BadUserPassword;

/**
 * Description of UtilisateurDAO
 *
 * @author User
 */
class UtilisateurDAO extends DAO
{
    /**
     * Crée un utilisateur à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Utilisateur
     */
    protected function buildBusinessObject($row)
    {
        $utilisateur = new Utilisateur();
        if ($row !== false) {
            $utilisateur->setUserId($row['USERID']);
            $utilisateur->setNom($row['NOM']);
            $utilisateur->setPrenom($row['PRENOM']);
            $utilisateur->setAdresseCourriel($row['ADRESSECOURRIEL']);
            $utilisateur->setPassword($row['PASSWORD']);
        }
        return $utilisateur;
    }

    /**
     * Retourne le BO Utilisateur en fonction de son identifiant
     * @param string $id
     * @throws \Exception
     */
    public function find(...$id)
    {
        $requete = "SELECT * FROM utilisateur WHERE userID = ?";
        $resultat = $this->getDb()->fetchAssoc(
            $requete,
            [$id[0]]
        );
        // si trouvé
        if ($resultat !== false) {
            // on récupère l'objet Film
            return $this->buildBusinessObject($resultat);
        } else {
            throw new BusinessObjectDoNotExist('Aucun utilisateur trouvé pour l\'id=' . $id[0]);
        }
    }

    /**
     * Retourne tous les utilisateurs de la BDD
     * @return array
     */
    public function findAll()
    {
        // requête d'extraction de tous les utilisateurs
        $sql = "SELECT * FROM utilisateur ORDER BY adresseCourriel ASC";
        $resultats = $this->getDb()->fetchAll($sql);

        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui teste si l'utilisateur est bien présent dans la BDD
     * @param string $email
     * @param string $passwordSaisiSaisi
     * @return Utilisateur L'objet métier Utilisateur
     * @throws BusinessObjectDoNotExist Si on ne trouve pas l'utilisateur en BDD
     */
    public function findOneByCourrielAndPassword($email, $passwordSaisi)
    {
        // extraction du mdp de l'utilisateur
        $requete = "SELECT * FROM utilisateur WHERE adresseCourriel = :email";
        // on prépare la requête

        $result = $this->getDb()->fetchAssoc(
            $requete,
            ['email' => $email]
        );

        // on teste le nombre de lignes renvoyées
        if ($result !== false && $result['PASSWORD'] !== '') {
            // on récupère le mot de passe
            $passwordBDD = $result['PASSWORD'];
            if ($this->testPasswords($passwordSaisi, $passwordBDD, $email) === true) {
                return $this->buildBusinessObject($result);
            }
        } else {
            throw new BusinessObjectDoNotExist('The user ' . $email . ' doesn\'t exist.');
        }
    }

    /**
     * Teste si le password saisi correspond bien à celui de l'utilisateur
     * @param string $passwordSaisi
     * @param string $passwordBDD
     * @param string $email
     * @return boolean
     * @throws \BadUserPassword
     */
    private function testPasswords($passwordSaisi, $passwordBDD, $email)
    {
        // on teste si les mots de passe correspondent
        if (
            password_verify(
                $passwordSaisi,
                $passwordBDD
            ) === true
        ) {
            if ($this->logger !== null) {
                $this->logger->info('User ' . $email . ' now connected.');
            }
            return true;
        } else {
            throw new BadUserPassword('Bad password for the user ' . $email);
        }
    }

    /*
     * Méthode qui retourne l'utilisateur initialisé
     *
     * @param string $email Adresse email de l'utilisateur
     * @return Utilisateur L'Utilisateur initialisé
     */
    public function findOneByCourriel($email)
    {
        // on construit la requête qui va récupérer les informations de l'utilisateur
        $requete = "SELECT * FROM utilisateur "
                . "WHERE adresseCourriel = :email";

        // on extrait le résultat de la BDD sous forme de tableau associatif
        $resultat = $this->getDb()->fetchAssoc(
            $requete,
            ['email' => $email]
        );

        // on construit l'objet Utilisateur
        $utilisateur = $this->buildBusinessObject($resultat);

        // on retourne l'utilisateur
        return $utilisateur;
    }

    /**
     * Sauvegarde un BO Utilisateur en BDD
     * @param Utilisateur $utilisateur
     */
    public function save(Utilisateur $utilisateur)
    {
        // je récupère les données de l'utilisateur sous forme de tableau
        $donneesUtilisateur = [
            'prenom'          => $utilisateur->getPrenom(),
            'nom'             => $utilisateur->getNom(),
            'adresseCourriel' => $utilisateur->getAdresseCourriel(),
            'password'        => $utilisateur->getPassword(),
        ];

        // Sinon, nous faisons une insertion
        $this->getDb()->insert('utilisateur', $donneesUtilisateur);
        // On récupère l'id autoincrement
        $id = $this->getDb()->lastInsertId();
        // affectation
        $utilisateur->setUserId($id);
    }
}
