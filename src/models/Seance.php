<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\includes\DAO;

/**
 * Description of Seance
 *
 * @author User
 */
class Seance extends DAO {
    
    protected function buildBusinessObject($row) {
        null;
    }

    public function getMovieShowtimes($cinemaID, $filmID) {
        // requête qui permet de récupérer la liste des séances d'un film donné dans un cinéma donné
        $requete = "SELECT s.* FROM seance s"
                . " WHERE s.filmID = :filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultat = $this->extraireNxN($requete,
                array(
            'filmID' => $filmID,
            'cinemaID' => $cinemaID));
        // on retourne la requête
        return $resultat;
    }

    /*
     * Méthode qui retourne toutes les séances de tous les films présents dans un cinéma donné
     * @param array $films Liste des films du cinéma donné
     * @param int $cinemaID Identifiant du cinéma concerné
     * @return Les séances des films projetés dans ce cinéma
     */

    public function getAllMoviesShowtimesByCinemaID($films, $cinemaID) {
        $seances = array(
            array());
        // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
        foreach ($films as $film) {
            $seances[$film->getFilmId()] = $this->getMovieShowtimes($cinemaID,
                    $film->getFilmId());
        }
        // on retourne le résultat
        return $seances;
    }

    /*
     * Méthode qui retourne toutes les séances de tous les cinémas d'un film donné
     * @param array $cinemas Liste des cinémas qui projettent ce film
     * @param int $filmID Identifiant du film concerné
     * @return Les séances du film projeté dans ces cinémas
     */

    public function getAllCinemasShowtimesByMovieID($cinemas, $filmID) {
        $seances = array(
            array());
        // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
        foreach ($cinemas as $cinema) {
            $seances[$cinema->getCinemaId()] = $this->getMovieShowtimes($cinema->getCinemaId(),
                    $filmID);
        }
        // on retourne le résultat
        return $seances;
    }

}
