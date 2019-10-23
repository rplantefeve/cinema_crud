<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\includes\Model;

/**
 * Description of Film
 *
 * @author User
 */
class Film extends Model
{
    /*
     * Méthode qui renvoie la liste des films
     * @return array[][]
     */

    public function getMoviesList()
    {
        $requete = "SELECT * FROM film";
        // on retourne le résultat
        return $this->extraireNxN(
            $requete,
            null,
            false
        );
    }

    /*
     * Méthode qui renvoie toutes les informations d'un film
     * @return array[]
     */

    public function getMovieInformationsByID($filmID)
    {
        $requete = "SELECT * FROM film WHERE filmID = "
                . $filmID;
        $resultat = $this->extraire1xN($requete);
        // on retourne le résultat extrait
        return $resultat;
    }

    public function getCinemaMoviesByCinemaID($cinemaID)
    {
        // requête qui nous permet de récupérer la liste des films pour un cinéma donné
        $requete = "SELECT DISTINCT f.* FROM film f"
                . " INNER JOIN seance s ON f.filmID = s.filmID"
                . " AND s.cinemaID = " . $cinemaID;
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on retourne le résultat
        return $resultat;
    }
    /**
     *
     * @param type $titre
     * @param type $titreOriginal
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
            ['titre' => $titre,
            'titreOriginal' => $titreOriginal]
        );
        // log
        if ($this->logger) {
            $this->logger->info('Movie ' . $titre . ' successfully added.');
        }
    }

    /**
     *
     * @param type $filmID
     * @param type $titre
     * @param type $titreOriginal
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
     *
     * @param type $movieID
     */
    public function deleteMovie($movieID)
    {
        $this->executeQuery("DELETE FROM film WHERE filmID = "
                . $movieID);

        if ($this->logger) {
            $this->logger->info('Movie ' . $movieID . ' successfully deleted.');
        }
    }

    /**
     * Renvoie une liste de cinémas qui ne projettent pas le film donné
     * @param integer $filmID
     * @return array
     */
    public function getNonPlannedCinemas($filmID)
    {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été programmés dans ce cinéma
        $requete = "SELECT c.cinemaID, c.denomination "
                . "FROM cinema c"
                . " WHERE c.cinemaID NOT IN ("
                . "SELECT cinemaID"
                . " FROM seance"
                . " WHERE filmID = :id"
                . ")";
        // extraction de résultat
        $resultat = $this->extraireNxN($requete, ['id' => $filmID], false);
        // retour du résultat
        return $resultat;
    }
}
