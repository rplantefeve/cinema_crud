<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Prefere;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO;
use Semeformation\Mvc\Cinema_crud\exceptions\BusinessObjectDoNotExist;

/**
 * Description of PrefereDAO
 *
 * @author User
 */
class PrefereDAO extends DAO {

    /**
     * DAO Film
     * @var \Semeformation\Mvc\Cinema_crud\dao\FilmDAO 
     */
    private $filmDAO;

    /**
     * DAO Utilisateur
     * @var \Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO; 
     */
    private $utilisateurDAO;

    /**
     * Crée une préférence à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Prefere
     */
    protected function buildBusinessObject($row) {
        $prefere = new Prefere();
        $prefere->setCommentaire($row['COMMENTAIRE']);
        // trouver l'utilisateur concerné grâce à son identifiant
        if (array_key_exists('USERID', $row)) {
            $userId      = $row['USERID'];
            $utilisateur = $this->utilisateurDAO->find($userId);
            $prefere->setUtilisateur($utilisateur);
        }
        // trouver le film concerné grâce à son identifiant
        if (array_key_exists('FILMID', $row)) {
            $filmId = $row['FILMID'];
            $film   = $this->filmDAO->find($filmId);
            $prefere->setFilm($film);
        }
        // on retourne l'objet métier ainsi "hydraté"
        return $prefere;
    }

    /**
     * Méthode qui renvoie les informations sur un film favori donné pour un utilisateur donné
     * @param type $userIdAndFilmId
     * @return type
     * @throws Exception
     */
    public function find(...$userIdAndFilmId) {
        // requête qui récupère les informations d'une préférence de film pour un utilisateur donné
        $requete  = "SELECT f.TITRE, p.*"
                . " FROM prefere p INNER JOIN film f ON p.filmID = f.filmID"
                . " WHERE p.userID = ? AND p.filmID = ?";
        $resultat = $this->getDb()->fetchAssoc($requete,
                [
            $userIdAndFilmId[0],
            $userIdAndFilmId[1]]);
        // si trouvé
        if ($resultat) {
            // on récupère et on retourne l'objet préférence
            return $this->buildBusinessObject($resultat);
        } else {
            throw new BusinessObjectDoNotExist('Aucune préférence trouvée pour l\'utilisateur d\'id=' . $userIdAndFilmId[0] . ' pour le film d\'id=' . $userIdAndFilmId[1]);
        }
    }

    public function findAll() {
        // requête d'extraction de toutes les préférences
        $sql       = "SELECT * FROM prefere";
        $resultats = $this->getDb()->fetchAll($sql);

        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui retourne les films préférés d'un utilisateur donné
     * @param string $utilisateur Adresse email de l'utilisateur
     * @return array[][] Les films préférés (sous forme de tableau associatif) de l'utilisateur
     */
    public function findAllByUserId($id) {
        // on construit la requête qui va récupérer les films de l'utilisateur
        $requete = "SELECT f.FILMID, f.TITRE, p.COMMENTAIRE, p.USERID from film f" .
                " INNER JOIN prefere p ON f.filmID = p.filmID" .
                " AND p.userID = :userID"
                . " ORDER BY f.TITRE ASC";

        // on extrait le résultat de la BDD sous forme de tableau associatif
        $resultats = $this->getDb()->fetchAll($requete,
                [
            'userID' => $id]);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Sauvegarde un objet Prefere en BDD
     * @param Prefere $prefere
     */
    public function save(Prefere $prefere) {
        // je récupère les données de l'objet métier sous forme de tableau
        $donneesPrefere = array(
            'filmId'      => $prefere->getFilm()->getFilmId(),
            'userId'      => $prefere->getUtilisateur()->getUserId(),
            'commentaire' => $prefere->getCommentaire()
        );

        try {
            // la préférence existe-t-elle ?
            $existe = $this->find($donneesPrefere['userId'],
                    $donneesPrefere['filmId']);
            // il faut faire une mise à jour
            $this->getDb()->update('prefere', $donneesPrefere,
                    array(
                'filmId' => $prefere->getFilm()->getFilmId(),
                'userId' => $prefere->getUtilisateur()->getUserId()));
        } catch (BusinessObjectDoNotExist $e) {
            // Sinon, nous faisons une insertion
            $this->getDb()->insert('prefere', $donneesPrefere);
        }
    }

    /**
     * Supprime une préférence de film
     * @param type $userID
     * @param type $filmID
     */
    public function delete($userID, $filmID) {
        $this->getDb()->delete('prefere',
                array(
            'userId' => $userID,
            'filmId' => $filmID));
    }

    public function getFilmDAO() {
        return $this->filmDAO;
    }

    public function getUtilisateurDAO() {
        return $this->utilisateurDAO;
    }

    public function setFilmDAO(FilmDAO $filmDAO) {
        $this->filmDAO = $filmDAO;
    }

    public function setUtilisateurDAO(UtilisateurDAO $utilisateurDAO) {
        $this->utilisateurDAO = $utilisateurDAO;
    }

}
