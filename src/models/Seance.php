<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\includes\Model;

/**
 * Description of Seance
 *
 * @author User
 */
class Seance extends Model {

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
        if($films !== null &&  count($films) > 0){
            foreach ($films as $film) {
                $seances[$film['FILMID']] = $this->getMovieShowtimes($cinemaID,
                        $film['FILMID']);
            }
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
    public function getAllCinemasShowtimesByMovieID($cinemas, $filmID)
    {
        $seances = [];
        if($cinemas !== null &&  count($cinemas) > 0){
            // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
            foreach ($cinemas as $cinema) {
                $seances[$cinema['CINEMAID']] = $this->getMovieShowtimes($cinema['CINEMAID'], $filmID);
            }
        }
        // on retourne le résultat
        return $seances;
    }
}
