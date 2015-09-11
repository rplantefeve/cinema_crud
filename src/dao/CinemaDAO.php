<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Exception;

/**
 * Description of CinemaDAO
 *
 * @author User
 */
class CinemaDAO extends DAO {

    /**
     * Crée un cinéma à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Cinema
     */
    private function buildCinema($row) {
        $cinema = new Cinema();
        $cinema->setCinemaId($row['CINEMAID']);
        $cinema->setDenomination($row['DENOMINATION']);
        $cinema->setAdresse($row['ADRESSE']);
        return $cinema;
    }

    private function buildCinemas($rows) {
        foreach ($rows as $row) {
            $cinemas[] = $this->buildCinema($row);
        }
        return $cinemas;
    }

    public function getCinemaByID($cinemaID) {
        $requete = "SELECT * FROM cinema WHERE cinemaID = "
                . $cinemaID;
        $resultat = $this->extraire1xN($requete);
        // on crée l'objet métier Cinema
        $cinema = $this->buildCinema($resultat);
        // on retourne le résultat extrait
        return $cinema;
    }

    public function getMovieCinemasByMovieID($filmID) {
        // requête qui nous permet de récupérer la liste des cinémas pour un film donné
        $requete = "SELECT DISTINCT c.* FROM cinema c"
                . " INNER JOIN seance s ON c.cinemaID = s.cinemaID"
                . " AND s.filmID = " . $filmID;
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on récupère tous les objets cinema concernés
        $cinemas = $this->buildCinemas($resultat);
        // on retourne le résultat
        return $cinemas;
    }

    public function getCinemasList() {
        $requete = "SELECT * FROM cinema";
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on récupère tous les objets cinema
        $cinemas = $this->buildCinemas($resultat);
        // on retourne le résultat
        return $cinemas;
    }

}
