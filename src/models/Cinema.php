<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\includes\Model;

/**
 * Description of Cinema
 *
 * @author User
 */
class Cinema extends Model
{
    public function getCinemasList()
    {
        $requete = "SELECT * FROM cinema";
        // on retourne le résultat
        return $this->extraireNxN($requete);
    }

    public function getCinemaInformationsByID($cinemaID)
    {
        $requete = "SELECT * FROM cinema WHERE cinemaID = "
                . $cinemaID;
        $resultat = $this->extraire1xN($requete);
        // on retourne le résultat extrait
        return $resultat;
    }

    public function getMovieCinemasByMovieID($filmID)
    {
        // requête qui nous permet de récupérer la liste des cinémas pour un film donné
        $requete = "SELECT DISTINCT c.* FROM cinema c"
                . " INNER JOIN seance s ON c.cinemaID = s.cinemaID"
                . " AND s.filmID = " . $filmID;
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on retourne le résultat
        return $resultat;
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
        // retour du résultat
        return $resultat;
    }

    /**
     *
     * @param type $denomination
     * @param type $adresse
     */
    public function insertNewCinema($denomination, $adresse)
    {
        // construction
        $requete = "INSERT INTO cinema (denomination, adresse) VALUES ("
                . ":denomination"
                . ", :adresse)";
        // exécution
        $this->executeQuery(
            $requete,
            ['denomination' => $denomination,
            'adresse' => $adresse]
        );
        // log
        if ($this->logger) {
            $this->logger->info('Cinema ' . $denomination . ' successfully added.');
        }
    }

    /**
     *
     * @param type $cinemaID
     * @param type $denomination
     * @param type $adresse
     */
    public function updateCinema($cinemaID, $denomination, $adresse)
    {
        // on construit la requête d'insertion
        $requete = "UPDATE cinema SET "
                . "denomination = "
                . "'" . $denomination . "'"
                . ", adresse = "
                . "'" . $adresse . "'"
                . " WHERE cinemaID = "
                . $cinemaID;
        // exécution de la requête
        $this->executeQuery($requete);
    }

    /**
     *
     * @param type $cinemaID
     */
    public function deleteCinema($cinemaID)
    {
        $this->executeQuery("DELETE FROM cinema WHERE cinemaID = "
                . $cinemaID);

        if ($this->logger) {
            $this->logger->info('Cinema ' . $cinemaID . ' successfully deleted.');
        }
    }
}
