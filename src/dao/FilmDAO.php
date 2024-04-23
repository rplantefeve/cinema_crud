<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Film;

/**
 * Description of FilmDAO
 *
 * @author User
 */
class FilmDAO extends DAO
{
    /**
     * Crée un film à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Film
     */
    protected function buildBusinessObject($row)
    {
        $film = new Film();
        $film->setFilmId($row['FILMID']);
        $film->setTitre($row['TITRE']);
        if (array_key_exists('TITREORIGINAL', $row) === true) {
            $film->setTitreOriginal($row['TITREORIGINAL']);
        }
        return $film;
    }

    /**
     * Méthode qui renvoie la liste des films
     * @return array<Film>
     */
    public function getMoviesList(): array
    {
        $requete = "SELECT * FROM film";
        // on extrait les résultats
        $resultats = $this->extraireNxN($requete);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui renvoie toutes les informations d'un film
     * @return Film
     */
    public function getMovieByID($filmID)
    {
        $requete = "SELECT * FROM film WHERE filmID = :filmID";
        $resultat = $this->extraire1xN($requete, ['filmID' => $filmID]);
        // on récupère l'objet Film
        $film = $this->buildBusinessObject($resultat);
        // on retourne le résultat extrait
        return $film;
    }

    public function getCinemaMoviesByCinemaID($cinemaID)
    {
        // requête qui nous permet de récupérer la liste des films pour un cinéma donné
        $requete = "SELECT DISTINCT f.* FROM film f"
                . " INNER JOIN seance s ON f.filmID = s.filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultats = $this->extraireNxN($requete, ['cinemaID' => $cinemaID]);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui ne renvoie que les films non encore marqués
     * comme favoris par l'utilisateur passé en paramètre
     * @param int $userID Identifiant de l'utilisateur
     * @return Film[] Films présents dans la base respectant les critères
     */
    public function getMoviesNonAlreadyMarkedAsFavorite($userID)
    {
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
        $resultats = $this->extraireNxN($requete, ['id' => $userID], false);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Renvoie une liste de films pas encore programmés pour un cinema donné
     * @param integer $cinemaID
     * @return array
     */
    public function getNonPlannedMovies($cinemaID)
    {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été programmés dans ce cinéma
        $requete = "SELECT f.filmID, f.titre "
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
    public function insertNewMovie($titre, $titreOriginal = null)
    {
        // construction
        $requete = "INSERT INTO film (titre, titreOriginal) VALUES ("
                . ":titre"
                . ", :titreOriginal)";
        // exécution
        $this->executeQuery(
            $requete,
            [
                'titre'         => $titre,
                'titreOriginal' => $titreOriginal,
            ]
        );
        // log
        if ($this->logger !== null) {
            $this->logger->info('Movie ' . $titre . ' successfully added.');
        }
    }

    /**
     * Met un jour un film
     * @param integer $filmID
     * @param string $titre
     * @param string $titreOriginal
     */
    public function updateMovie($filmID, $titre, $titreOriginal)
    {
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
    public function deleteMovie($movieID)
    {
        $this->executeQuery(
            "DELETE FROM film WHERE filmID = "
            . $movieID
        );

        if ($this->logger !== null) {
            $this->logger->info('Movie ' . $movieID . ' successfully deleted.');
        }
    }
}
