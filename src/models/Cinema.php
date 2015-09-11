<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\includes\DAO;

/**
 * Description of Cinema
 *
 * @author User
 */
class Cinema extends DAO {

    public function getCinemasList() {
        $requete = "SELECT * FROM cinema";
        // on retourne le résultat
        return $this->extraireNxN($requete);
    }

    public function getCinemaInformationsByID($cinemaID) {
        $requete = "SELECT * FROM cinema WHERE cinemaID = "
                . $cinemaID;
        $resultat = $this->extraire1xN($requete);
        // on retourne le résultat extrait
        return $resultat;
    }



    public function getMovieCinemasByMovieID($filmID) {
        // requête qui nous permet de récupérer la liste des cinémas pour un film donné
        $requete = "SELECT DISTINCT c.* FROM cinema c"
                . " INNER JOIN seance s ON c.cinemaID = s.cinemaID"
                . " AND s.filmID = " . $filmID;
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on retourne le résultat
        return $resultat;
    }

}
