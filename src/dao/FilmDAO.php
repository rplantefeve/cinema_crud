<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Film;
use Semeformation\Mvc\Cinema_crud\exceptions\BusinessObjectDoNotExist;

/**
 * Description of FilmDAO
 *
 * @author User
 */
class FilmDAO extends DAO {

    /**
     * Crée un film à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Film
     */
    protected function buildBusinessObject($row) {
        $film = new Film();
        $film->setFilmId($row['FILMID']);
        $film->setTitre($row['TITRE']);
        if (array_key_exists('TITREORIGINAL', $row)) {
            $film->setTitreOriginal($row['TITREORIGINAL']);
        }
        return $film;
    }

    /**
     * Retourne le BO Film en fonction de son identifiant
     * @param type $filmId
     * @return type
     * @throws Exception
     */
    public function find(...$filmId) {
        $requete  = "SELECT * FROM film WHERE filmID = ?";
        $resultat = $this->getDb()->fetchAssoc($requete, [
            $filmId[0]]);
        // si trouvé
        if ($resultat) {
            // on récupère et on retourne l'objet Film
            return $this->buildBusinessObject($resultat);
        } else {
            throw new BusinessObjectDoNotExist('Aucun film trouvé pour l\'id=' . $filmId[0]);
        }
    }

    /**
     * Retourne tous les films de la base de données
     * @return array
     */
    public function findAll() {
        // requête d'extraction de tous les films
        $sql       = "SELECT * FROM film ORDER BY titre ASC";
        $resultats = $this->getDb()->fetchAll($sql);

        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Retourne les films d'un cinéma
     * @param type $cinemaID
     * @return array Tableau d'objes Film
     */
    public function findAllByCinemaId($cinemaID) {
        // requête qui nous permet de récupérer la liste des films pour un cinéma donné
        $requete   = "SELECT DISTINCT f.* FROM film f"
                . " INNER JOIN seance s ON f.filmID = s.filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultats = $this->getDb()->fetchAll($requete,
                [
            'cinemaID' => $cinemaID]);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui ne renvoie que les films non encore marqués
     * comme favoris par l'utilisateur passé en paramètre
     * @param int $userID Identifiant de l'utilisateur
     * @return Film[] Films présents dans la base respectant les critÃ¨res
     */
    public function findAllByUserIdNotIn($userID) {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été marqués comme favoris par l'utilisateur
        $requete   = "SELECT f.FILMID, f.TITRE "
                . "FROM film f"
                . " WHERE f.filmID NOT IN ("
                . "SELECT filmID"
                . " FROM prefere"
                . " WHERE userID = :id"
                . ")";
        // extraction de résultat
        $resultats = $this->getDb()->fetchAll($requete, [
            'id' => $userID]);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Renvoie une liste de films pas encore programmés pour un cinema donné
     * @param integer $cinemaID
     * @return array
     */
    public function findAllByCinemaIdNotIn($cinemaID) {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été programmés dans ce cinéma
        $requete  = "SELECT f.FILMID, f.TITRE "
                . "FROM film f"
                . " WHERE f.filmID NOT IN ("
                . "SELECT filmID"
                . " FROM seance"
                . " WHERE cinemaID = :id"
                . ")";
        // extraction de résultat
        $resultat = $this->getDb()->fetchAll($requete, [
            'id' => $cinemaID]);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultat);
    }

    /**
     * Sauvegarde un objet Film en BDD
     * @param Film $film
     */
    public function save(Film $film) {
        // je récupère les données du film sous forme de tableau
        $donneesFilm = array(
            'titre'         => $film->getTitre(),
            'titreOriginal' => $film->getTitreOriginal(),
        );

        // Si le film existe déja
        if ($film->getFilmId()) {
            // il faut faire une mise à jour
            $this->getDb()->update('film', $donneesFilm,
                    array(
                'filmId' => $film->getFilmId()));
        } else {
            // Sinon, nous faisons une insertion
            $this->getDb()->insert('film', $donneesFilm);
            // On récupère l'id autoincrement
            $id = $this->getDb()->lastInsertId();
            // affectation
            $film->setFilmId($id);
        }
    }

    /**
     * Supprime un film
     * @param integer $movieID
     */
    public function delete($movieID) {
        $this->getDb()->delete('film', array(
            'filmId' => $movieID));
    }

}
