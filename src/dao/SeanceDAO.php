<?php

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
class SeanceDAO extends DAO
{
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

    public function getFilmDAO(): FilmDAO
    {
        return $this->filmDAO;
    }

    public function getCinemaDAO(): CinemaDAO
    {
        return $this->cinemaDAO;
    }

    public function setFilmDAO(FilmDAO $filmDAO): void
    {
        $this->filmDAO = $filmDAO;
    }

    public function setCinemaDAO(CinemaDAO $cinemaDAO): void
    {
        $this->cinemaDAO = $cinemaDAO;
    }

    /**
     * Crée une séance à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Seance
     */
    protected function buildBusinessObject($row)
    {
        $seance = new Seance();
        $seance->setHeureDebut(new DateTime($row['HEUREDEBUT']));
        $seance->setHeureFin(new DateTime($row['HEUREFIN']));
        $seance->setVersion($row['VERSION']);
        // trouver le film concerné grâce à son identifiant
        if (array_key_exists('FILMID', $row) === true) {
            $filmID = $row['FILMID'];
            $film = $this->filmDAO->getMovieByID($filmID);
            $seance->setFilm($film);
        }
        // trouver le cinéma concerné grâce à son identifiant
        if (array_key_exists('CINEMAID', $row) === true) {
            $cinemaID = $row['CINEMAID'];
            $cinema = $this->cinemaDAO->getCinemaByID($cinemaID);
            $seance->setCinema($cinema);
        }
        return $seance;
    }

    /**
     * @param int $cinemaID
     * @param int $filmID
     *
     * @return array<object>|null
     */
    public function getMovieShowtimes($cinemaID, $filmID): array
    {
        // requête qui permet de récupérer la liste des séances d'un film donné dans un cinéma donné
        $requete = "SELECT s.* FROM seance s"
                . " WHERE s.filmID = :filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultats = $this->extraireNxN(
            $requete,
            [
                'filmID'   => $filmID,
                'cinemaID' => $cinemaID,
            ]
        );
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui retourne toutes les séances de tous les films présents dans un cinéma donné
     * @param array $films Liste des films du cinéma donné
     * @param int $cinemaID Identifiant du cinéma concerné
     * @return array<array<Seance>> Les séances des films projetées dans ce cinéma
     */
    public function getAllMoviesShowtimesByCinemaID($films, $cinemaID): array
    {
        $seances = [];
        if ($films !== null && count($films) > 0) :
            // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
            foreach ($films as $film) {
                $seances[$film->getFilmId()] = $this->getMovieShowtimes(
                    $cinemaID,
                    $film->getFilmId()
                );
            }
        endif;
        // on retourne le résultat
        return $seances;
    }

    /**
     * Méthode qui retourne toutes les séances de tous les cinémas d'un film donné
     * @param array $cinemas Liste des cinémas qui projettent ce film
     * @param int $filmID Identifiant du film concerné
     * @return array<array<Seance>> Les séances du film projeté dans ces cinémas
     */
    public function getAllCinemasShowtimesByMovieID($cinemas, $filmID): array
    {
        $seances = [];
        if ($cinemas !== null && count($cinemas) > 0) {
            // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
            foreach ($cinemas as $cinema) {
                $seances[$cinema->getCinemaId()] = $this->getMovieShowtimes(
                    $cinema->getCinemaId(),
                    $filmID
                );
            }
        }
        // on retourne le résultat
        return $seances;
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
            [
                ':cinemaID'   => $cinemaID,
                ':filmID'     => $filmID,
                ':heureDebut' => $dateheuredebut,
                ':heureFin'   => $dateheurefin,
                ':version'    => $version,
            ]
        );

        // log
        if ($this->logger !== null) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' at the ' . $cinemaID . ' successfully added.');
        }

        return $resultat;
    }

    /**
     * Met à jour une séance pour un film donné dans un cinéma donné
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
            [
                ':cinemaID'      => $cinemaID,
                ':filmID'        => $filmID,
                ':heureDebutOld' => $dateheuredebutOld,
                ':heureFinOld'   => $dateheurefinOld,
                ':heureDebut'    => $dateheuredebut,
                ':heureFin'      => $dateheurefin,
                ':version'       => $version,
            ]
        );

        // log
        if ($this->logger !== null) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' at the ' . $cinemaID . ' successfully updated.');
        }

        return $resultat;
    }

    /**
     * Supprime une séance pour un film donné et un cinéma donné
     *
     * @param int $cinemaID
     * @param int $filmID
     * @param string $heureDebut
     * @param string $heureFin
     */
    public function deleteShowtime($cinemaID, $filmID, $heureDebut, $heureFin): void
    {
        $this->executeQuery(
            "DELETE FROM seance "
                . "WHERE cinemaID = :cinemaID "
                . "AND filmID = :filmID "
                . "AND heureDebut = :heureDebut"
                . " AND heureFin = :heureFin",
            [
                ':cinemaID'   => $cinemaID,
                ':filmID'     => $filmID,
                ':heureDebut' => $heureDebut,
                ':heureFin'   => $heureFin,
            ]
        );

        if ($this->logger !== null) {
            $this->logger->info('Showtime for the movie ' . $filmID . ' and the cinema ' . $cinemaID . ' successfully deleted.');
        }
    }
}
