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
     * Retourne un BO Séance en fonction de l'id du cinéma, du film, de l'heure de début et de fin
     * @param type $primaryKey
     * @return Seance
     * @throws \Exception
     */
    public function find(...$primaryKey) {
        $requete  = "SELECT * FROM seance "
                . "WHERE cinemaID = ? "
                . "AND filmID = ? "
                . "AND heureDebut = ? "
                . "AND heureFin = ?";
        $resultat = $this->getDb()->fetchAssoc($requete, $primaryKey[0],
                $primaryKey[1], $primaryKey[2], $primaryKey[3]);
        // si trouvé
        if ($resultat) {
            return $this->buildBusinessObject($resultat);
        } else {
            throw new \Exception('Pas de séance trouvé pour le film id=' . $primaryKey[0] . ', cinema id=' . $primaryKey[1] . ', heure de début=' . $primaryKey[2] . ', heure de fin=' . $primaryKey[3]);
        }
    }

    /**
     * Retourne toutes les séances de la BDD
     * @return array
     */
    public function findAll() {
        // requête d'extraction de toutes les séances
        $sql       = "SELECT * FROM seance";
        $resultats = $this->getDb()->fetchAll($sql);

        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
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
        if (array_key_exists('FILMID', $row)) {
            $filmID = $row['FILMID'];
            $film   = $this->filmDAO->find($filmID);
            $seance->setFilm($film);
        }
        // trouver le cinéma concerné grâce à son identifiant
        if (array_key_exists('CINEMAID', $row)) {
            $cinemaID = $row['CINEMAID'];
            $cinema   = $this->cinemaDAO->find($cinemaID);
            $seance->setCinema($cinema);
        }
        return $seance;
    }

    /**
     * Retourne les séances d'un film donné dans un cinéma donné
     * @param type $cinemaID
     * @param type $filmID
     * @return array
     */
    public function findAllByCinemaIdAndFilmId($cinemaID, $filmID) {
        // requête qui permet de récupérer la liste des séances d'un film donné dans un cinéma donné
        $requete   = "SELECT s.* FROM seance s"
                . " WHERE s.filmID = :filmID"
                . " AND s.cinemaID = :cinemaID";
        // on extrait les résultats
        $resultats = $this->getDb()->fetchAll($requete,
                array(
            'filmID'   => $filmID,
            'cinemaID' => $cinemaID));
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Méthode qui retourne toutes les séances de tous les films présents dans un cinéma donné
     * @param array $films Liste des films du cinéma donné
     * @param int $cinemaID Identifiant du cinéma concerné
     * @return Les séances des films projetés dans ce cinéma
     */
    public function findAllByCinemaId($films, $cinemaID) {
        if ($films):
            // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
            foreach ($films as $film) {
                $seances[$film->getFilmId()] = $this->findAllByCinemaIdAndFilmId($cinemaID,
                        $film->getFilmId());
            }
            // on retourne le résultat
            return $seances;
        else:
            return null;
        endif;
    }

    /**
     * Méthode qui retourne toutes les séances de tous les cinémas d'un film donné
     * @param array $cinemas Liste des cinémas qui projettent ce film
     * @param int $filmID Identifiant du film concerné
     * @return Les séances du film projeté dans ces cinémas
     */
    public function findAllByFilmId($cinemas, $filmID) {
        $seances = null;
        // Boucle de récupération de toutes les séances indexés sur l'identifiant du film
        foreach ($cinemas as $cinema) {
            $seances[$cinema->getCinemaId()] = $this->findAllByCinemaIdAndFilmId($cinema->getCinemaId(),
                    $filmID);
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
    public function insertNewShowtime($cinemaID, $filmID, $dateheuredebut,
            $dateheurefin, $version): \PDOStatement {
        // construction
        $requete  = "INSERT INTO seance (cinemaID, filmID, heureDebut, heureFin, version) VALUES ("
                . ":cinemaID"
                . ", :filmID"
                . ", :heureDebut"
                . ", :heureFin"
                . ", :version)";
        // exécution
        $resultat = $this->getDb()->executeQuery($requete,
                [
            ':cinemaID'   => $cinemaID,
            ':filmID'     => $filmID,
            ':heureDebut' => $dateheuredebut,
            ':heureFin'   => $dateheurefin,
            ':version'    => $version]);

        // log
        if ($this->logger) {
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
    public function updateShowtime($cinemaID, $filmID, $dateheuredebutOld,
            $dateheurefinOld, $dateheuredebut, $dateheurefin, $version): \PDOStatement {
        // construction
        $requete  = "UPDATE seance SET heureDebut = :heureDebut,"
                . " heureFin = :heureFin,"
                . " version = :version"
                . " WHERE cinemaID = :cinemaID"
                . " AND filmID = :filmID"
                . " AND heureDebut = :heureDebutOld"
                . " AND heureFin = :heureFinOld";
        // exécution
        $resultat = $this->getDb()->executeQuery($requete,
                [
            ':cinemaID'      => $cinemaID,
            ':filmID'        => $filmID,
            ':heureDebutOld' => $dateheuredebutOld,
            ':heureFinOld'   => $dateheurefinOld,
            ':heureDebut'    => $dateheuredebut,
            ':heureFin'      => $dateheurefin,
            ':version'       => $version]);

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
    public function delete($cinemaID, $filmID, $heureDebut, $heureFin) {
        $this->getDb()->delete('seance',
                array(
            'cinemaId'   => $cinemaID,
            'filmID'     => $filmID,
            'heureDebut' => $heureDebut,
            'heureFin'   => $heureFin));
    }

}
