<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Cinema;

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
    protected function buildBusinessObject($row) {
        $cinema = new Cinema();
        $cinema->setCinemaId($row['CINEMAID']);
        $cinema->setDenomination($row['DENOMINATION']);
        $cinema->setAdresse($row['ADRESSE']);
        return $cinema;
    }

    /**
     * Recherche un cinéma à partir de son identifiant
     * @param string $cinemaID
     * @return Cinema
     * @throws Exception
     */
    public function find(...$cinemaID) {
        $requete  = "SELECT * FROM cinema WHERE cinemaID = ?";
        $resultat = $this->getDb()->fetchAssoc($requete, [$cinemaID[0]]);
        // si trouvé
        if ($resultat) {
            // on crée et on retourne l'objet métier Cinema
            return $this->buildBusinessObject($resultat);
        } else {
            throw new \Exception('Aucun cinéma trouvé pour l\'id=' . $cinemaID[0]);
        }
    }

    /**
     * Recherche tous les cinémas en BDD et retourne le résultat sous forme de tableau
     * @return array Le tableau de cinémas
     */
    public function findAll() {
        // requête d'extraction de tous les cinémas
        $sql       = "SELECT * FROM cinema ORDER BY denomination ASC";
        $resultats = $this->getDb()->fetchAll($sql);

        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Renvoie la liste des cinémas d'un film
     * @param integer $filmID
     * @return array Le tableau d'objets Cinema
     */
    public function findAllByFilmId($filmID) {
        // requête qui nous permet de récupérer la liste des cinémas pour un film donné
        $requete   = "SELECT DISTINCT c.* FROM cinema c"
                . " INNER JOIN seance s ON c.cinemaID = s.cinemaID"
                . " AND s.filmID = " . $filmID;
        // on extrait les résultats
        $resultats = $this->getDb()->fetchAll($requete);
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Renvoie une liste de cinémas qui ne projettent pas le film donné
     * @param integer $filmID
     * @return array Le tableau d'objets Cinema
     */
    public function findAllByFilmIdNotIn($filmID) {
        // requête de récupération des titres et des identifiants des films
        // qui n'ont pas encore été programmés dans ce cinéma
        $requete   = "SELECT c.CINEMAID, c.DENOMINATION, c.ADRESSE "
                . "FROM cinema c"
                . " WHERE c.cinemaID NOT IN ("
                . "SELECT cinemaID"
                . " FROM seance"
                . " WHERE filmID = :id"
                . ")";
        // extraction des résultats
        $resultats = $this->getDb()->fetchAll($requete, ['id' => $filmID]);
        // retour du résultat
        return $this->extractObjects($resultats);
    }

    /**
     * Sauvegarde un objet Cinema en BDD
     * @param Cinema $cinema
     */
    public function save(Cinema $cinema) {
        // je récupère les données du cinéma sous forme de tableau
        $donneesCinema = array(
            'denomination' => $cinema->getDenomination(),
            'adresse'      => $cinema->getAdresse(),
        );

        // Si le cinéma existe déja
        if ($cinema->getCinemaId()) {
            // il faut faire une mise à jour
            $this->getDb()->update('cinema', $donneesCinema,
                    array('cinemaId' => $cinema->getCinemaId()));
        } else {
            // Sinon, nous faisons une insertion
            $this->getDb()->insert('cinema', $donneesCinema);
            // On récupère l'id autoincrement
            $id = $this->getDb()->lastInsertId();
            // affectation
            $cinema->setCinemaId($id);
        }
    }

    /**
     * Supprime un cinéma
     * @param integer $cinemaID
     */
    public function delete($cinemaID) {
        // Supprime le cinéma
        $this->getDb()->delete('cinema', array('cinemaId' => $cinemaID));
    }

}
