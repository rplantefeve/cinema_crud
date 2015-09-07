<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\includes\DBFunctions;

/**
 * Description of Seance
 *
 * @author User
 */
class Seance extends DBFunctions {

    public function getMovieShowtimes($cinemaID, $filmID) {
        // requête qui permet de récupérer la liste des séances d'un film donné dans un cinéma donné
        $requete = "SELECT s.* FROM seance s"
                . " WHERE s.filmID = " . $filmID
                . " AND s.cinemaID = " . $cinemaID;
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete);
        // on retourne la requête
        return $resultat;
    }

}
