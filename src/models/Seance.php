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
                [
            'filmID' => $filmID,
            'cinemaID' => $cinemaID]);
        // on retourne la requête
        return $resultat;
    }

    /**
     * Insère une nouvelle séance pour un film donné dans un cinéma donné
     * @param integer $cinemaID
     * @param integer $filmID
     * @param datetime $dateheuredebut
     * @param datetime $dateheurefin
     * @param string $version
     */
    public function insertNewShowtime(
        $cinemaID,
        $filmID,
        $dateheuredebut,
            $dateheurefin,
        $version
    ): \PDOStatement {
        // construction
        $requete = "INSERT INTO seance (cinemaID, filmID, heureDebut, heureFin, version) VALUES ("
                . ":cinemaID"
                . ", :filmID"
                . ", :heureDebut"
                . ", :heureFin"
                . ", :version)";
        // exécution
        $resultat = $this->executeQuery(
            $requete,
                [':cinemaID' => $cinemaID,
            ':filmID' => $filmID,
            ':heureDebut' => $dateheuredebut,
            ':heureFin' => $dateheurefin,
            ':version' => $version]
        );

        // log
        if ($this->logger) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' at the ' . $cinemaID . ' successfully added.');
        }

        return $resultat;
    }

    /**
     * Insère une nouvelle séance pour un film donné dans un cinéma donné
     * @param integer $cinemaID
     * @param integer $filmID
     * @param datetime $dateheuredebutOld
     * @param datetime $dateheurefinOld
     * @param datetime $dateheuredebut
     * @param datetime $dateheurefin
     * @param string $version
     */
    public function updateShowtime(
        $cinemaID,
        $filmID,
        $dateheuredebutOld,
            $dateheurefinOld,
        $dateheuredebut,
        $dateheurefin,
        $version
    ): \PDOStatement {
        // construction
        $requete = "UPDATE seance SET heureDebut = :heureDebut,"
                . " heureFin = :heureFin,"
                . " version = :version"
                . " WHERE cinemaID = :cinemaID"
                . " AND filmID = :filmID"
                . " AND heureDebut = :heureDebutOld"
                . " AND heureFin = :heureFinOld";
        // exécution
        $resultat = $this->executeQuery(
            $requete,
                [':cinemaID' => $cinemaID,
            ':filmID' => $filmID,
            ':heureDebutOld' => $dateheuredebutOld,
            ':heureFinOld' => $dateheurefinOld,
            ':heureDebut' => $dateheuredebut,
            ':heureFin' => $dateheurefin,
            ':version' => $version]
        );

        // log
        if ($this->logger) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' at the ' . $cinemaID . ' successfully updated.');
        }

        return $resultat;
    }

    /**
     * Supprime une séance pour un film donné et un cinéma donné
     * @param type $cinemaID
     * @param type $filmID
     * @param type $heureDebut
     * @param type $heureFin
     */
    public function deleteShowtime($cinemaID, $filmID, $heureDebut, $heureFin)
    {
        $this->executeQuery(
            "DELETE FROM seance "
                . "WHERE cinemaID = :cinemaID "
                . "AND filmID = :filmID "
                . "AND heureDebut = :heureDebut"
                . " AND heureFin = :heureFin",
                [':cinemaID' => $cinemaID,
            ':filmID' => $filmID,
            ':heureDebut' => $heureDebut,
            ':heureFin' => $heureFin]
        );

        if ($this->logger) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' and the cinema ' . $cinemaID . ' successfully deleted.');
        }
    }

    /*
     * Méthode qui retourne toutes les séances de tous les films présents dans un cinéma donné
     * @param array $films Liste des films du cinéma donné
     * @param int $cinemaID Identifiant du cinéma concerné
     * @return Les séances des films projetés dans ce cinéma
     */

    public function getAllMoviesShowtimesByCinemaID($films, $cinemaID) {
        $seances = [];
        // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
        foreach ($films as $film) {
            $seances[$film['FILMID']] = $this->getMovieShowtimes($cinemaID,
                    $film['FILMID']);
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
        // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
        foreach ($cinemas as $cinema) {
            $seances[$cinema['CINEMAID']] = $this->getMovieShowtimes($cinema['CINEMAID'], $filmID);
        }
        // on retourne le résultat
        return $seances;
    }
}
