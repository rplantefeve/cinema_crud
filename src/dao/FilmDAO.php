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
        if (array_key_exists('TITREORIGINAL',
                        $row)) {
            $film->setTitreOriginal($row['TITREORIGINAL']);
        }
        return $film;
    }

    private function buildFilms($rows) {
        foreach ($rows as $row) {
            $films[] = $this->buildBusinessObject($row);
        }
        return $films;
    }

    /*
     * Méthode qui renvoie la liste des films
     * @return array[][]
     */

    public function getMoviesList() {
        $requete = "SELECT * FROM film";
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on récupère tous les objets Film
        $films = $this->buildFilms($resultat);
        // on retourne le résultat
        return $films;
    }

    /*
     * Méthode qui renvoie toutes les informations d'un film
     * @return array[]
     */

    public function getMovieByID($filmID) {
        $requete = "SELECT * FROM film WHERE filmID = :filmID";
        $resultat = $this->extraire1xN($requete,
                ['filmID' => $filmID]);
        // on récupère l'objet Film
        $film = $this->buildBusinessObject($resultat);
        // on retourne le résultat extrait
        return $film;
    }

    public function getCinemaMoviesByCinemaID($cinemaID) {
        // requête qui nous permet de récupérer la liste des films pour un cinéma donné
        $requete = "SELECT DISTINCT f.* FROM film f"
                . " INNER JOIN seance s ON f.filmID = s.filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete,
                ['cinemaID' => $cinemaID]);
        // on récupère tous les objets Film
        $films = $this->buildFilms($resultat);
        // on retourne le résultat
        return $films;
    }

    /*
     * Méthode qui ne renvoie que les films non encore marqués
     * comme favoris par l'utilisateur passé en paramètre
     * @param int $userID Identifiant de l'utilisateur
     * @return Film[] Films présents dans la base respectant les critères
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
        if (!is_null($resultat)) {
            // on crée les objets métiers
            $films = $this->buildFilms($resultat);
            // on retourne le résultat
            return $films;
        } else {
            return null;
        }
    }

}
