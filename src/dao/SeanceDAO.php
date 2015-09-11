<?php

//

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\models\Seance;
use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\CinemaDAO;
use DateTime;

/**
 * Description of SeanceDAO
 *
 * @author User
 */
class SeanceDAO extends DAO {

    /**
     *
     * @var FilmDAO
     */
    private $filmDAO;

    /**
     *
     * @var CinemaDAO
     */
    private $cinemaDAO;

    public function getFilmDAO() {
        return $this->filmDAO;
    }

    public function getCinemaDAO() {
        return $this->cinemaDAO;
    }

    public function setFilmDAO(FilmDAO $filmDAO) {
        $this->filmDAO = $filmDAO;
    }

    public function setCinemaDAO(CinemaDAO $cinemaDAO) {
        $this->cinemaDAO = $cinemaDAO;
    }

    /**
     * Crée une séance à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Seance
     */
    protected function buildBusinessObject($row) {
        $seance = new Seance();
        $seance->setHeureDebut(new DateTime($row['HEUREDEBUT']));
        $seance->setHeureFin(new DateTime($row['HEUREFIN']));
        $seance->setVersion($row['VERSION']);
        // trouver le film concerné grâce à son identifiant
        if (array_key_exists('FILMID',
                        $row)) {
            $filmID = $row['FILMID'];
            $film = $this->filmDAO->getMovieByID($filmID);
            $seance->setFilm($film);
        }
        // trouver le cinéma concerné grâce à son identifiant
        if (array_key_exists('CINEMAID',
                        $row)) {
            $cinemaID = $row['FILMID'];
            $cinema = $this->cinemaDAO->getCinemaByID($cinemaID);
            $seance->setCinema($cinema);
        }
        return $seance;
    }

    public function getMovieShowtimes($cinemaID, $filmID) {
        // requête qui permet de récupérer la liste des séances d'un film donné dans un cinéma donné
        $requete = "SELECT s.* FROM seance s"
                . " WHERE s.filmID = :filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultats = $this->extraireNxN($requete,
                array(
            'filmID' => $filmID,
            'cinemaID' => $cinemaID));
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /*
     * Méthode qui retourne toutes les séances de tous les films présents dans un cinéma donné
     * @param array $films Liste des films du cinéma donné
     * @param int $cinemaID Identifiant du cinéma concerné
     * @return Les séances des films projetés dans ce cinéma
     */

    public function getAllMoviesShowtimesByCinemaID($films, $cinemaID) {
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
        // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
        foreach ($cinemas as $cinema) {
            $seances[$cinema->getCinemaId()] = $this->getMovieShowtimes($cinema->getCinemaId(),
                    $filmID);
        }
        // on retourne le résultat
        return $seances;
    }

}
