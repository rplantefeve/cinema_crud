<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Film;

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
    public function find($filmId) {
        $requete  = "SELECT * FROM film WHERE filmID = ?";
        $resultat = $this->getDb()->fetchAssoc($requete, [$filmId]);
        // si trouvé
        if ($resultat) {
            // on récupère et on retourne l'objet Film
            return $this->buildBusinessObject($resultat);
        } else {
            throw new Exception('Aucun film trouvé pour l\'id=' . $filmId);
        }
    }

    public function findAll() {
        
    }

    /**
     * Méthode qui renvoie la liste des films
     * @return array[][]
     */
    public function getMoviesList() {
        $requete   = "SELECT * FROM film";
        // on extrait les résultats
        $resultats = $this->extraireNxN($requete);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui renvoie toutes les informations d'un film
     * @return array[]
     */
    public function getMovieByID($filmID) {
        
    }

    public function getCinemaMoviesByCinemaID($cinemaID) {
        // requête qui nous permet de récupérer la liste des films pour un cinéma donné
        $requete   = "SELECT DISTINCT f.* FROM film f"
                . " INNER JOIN seance s ON f.filmID = s.filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultats = $this->extraireNxN($requete, ['cinemaID' => $cinemaID]);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui ne renvoie que les films non encore marqués
     * comme favoris par l'utilisateur passé en paramÃ¨tre
     * @param int $userID Identifiant de l'utilisateur
     * @return Film[] Films présents dans la base respectant les critÃ¨res
     */
    public function getMoviesNonAlreadyMarkedAsFavorite($userID) {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été marqués comme favoris par l'utilisateur
        $requete   = "SELECT f.filmID, f.titre "
                . "FROM film f"
                . " WHERE f.filmID NOT IN ("
                . "SELECT filmID"
                . " FROM prefere"
                . " WHERE userID = :id"
                . ")";
        // extraction de résultat
        $resultats = $this->extraireNxN($requete, ['id' => $userID], false);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Renvoie une liste de films pas encore programmés pour un cinema donné
     * @param integer $cinemaID
     * @return array
     */
    public function getNonPlannedMovies($cinemaID) {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été programmés dans ce cinéma
        $requete  = "SELECT f.filmID, f.titre "
                . "FROM film f"
                . " WHERE f.filmID NOT IN ("
                . "SELECT filmID"
                . " FROM seance"
                . " WHERE cinemaID = :id"
                . ")";
        // extraction de résultat
        $resultat = $this->extraireNxN($requete, ['id' => $cinemaID], false);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultat);
    }

    /**
     * Crée un nouveau film
     * @param string $titre
     * @param string $titreOriginal
     */
    public function insertNewMovie($titre, $titreOriginal = null) {
        // construction
        $requete = "INSERT INTO film (titre, titreOriginal) VALUES ("
                . ":titre"
                . ", :titreOriginal)";
        // exécution
        $this->executeQuery($requete,
                [
            'titre'         => $titre,
            'titreOriginal' => $titreOriginal]);
        // log
        if ($this->logger) {
            $this->logger->info('Movie ' . $titre . ' successfully added.');
        }
    }

    /**
     * Met un jour un film
     * @param integer $filmID
     * @param string $titre
     * @param string $titreOriginal
     */
    public function updateMovie($filmID, $titre, $titreOriginal) {
        // on construit la requête d'insertion
        $requete = "UPDATE film SET "
                . "titre = "
                . "'" . $titre . "'"
                . ", titreOriginal = "
                . "'" . $titreOriginal . "'"
                . " WHERE filmID = "
                . $filmID;
        // exécution de la requête
        $this->executeQuery($requete);
    }

    /**
     * Supprime un film
     * @param integer $movieID
     */
    public function deleteMovie($movieID) {
        $this->executeQuery("DELETE FROM film WHERE filmID = "
                . $movieID);

        if ($this->logger) {
            $this->logger->info('Movie ' . $movieID . ' successfully deleted.');
        }
    }

}
