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
        return $this->extraireNxN($requete,
                        null,
                        false);
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

}
