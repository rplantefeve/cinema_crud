<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Prefere;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO;

/**
 * Description of PrefereDAO
 *
 * @author User
 */
class PrefereDAO extends DAO {

    private $filmDAO;
    private $utilisateurDAO;

    public function setFilmDAO(FilmDAO $filmDAO) {
        $this->filmDAO = $filmDAO;
    }

    public function setUtilisateurDAO(UtilisateurDAO $utilisateurDAO) {
        $this->utilisateurDAO = $utilisateurDAO;
    }

    /**
     * Crée une préférence à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Prefere
     */
    protected function buildBusinessObject($row) {
        $prefere = new Prefere();
        $prefere->setCommentaire($row['commentaire']);
        // trouver l'utilisateur concerné grâce à son identifiant
        if (array_key_exists('userID',
                        $row)) {
            $userId = $row['userID'];
            $utilisateur = $this->utilisateurDAO->getUserByID($userId);
            $prefere->setUtilisateur($utilisateur);
        }
        // trouver le film concerné grâce à son identifiant
        if (array_key_exists('filmID',
                        $row)) {
            $filmId = $row['filmID'];
            $film = $this->filmDAO->getMovieByID($filmId);
            $prefere->setFilm($film);
        }
        return $prefere;
    }

    private function buildPreferes($rows) {
        foreach ($rows as $row) {
            $preferes[] = $this->buildBusinessObject($row);
        }
        return $preferes;
    }

    /*
     * Méthode qui retourne les films préférés d'un utilisateur donné
     * @param string $utilisateur Adresse email de l'utilisateur
     * @return array[][] Les films préférés (sous forme de tableau associatif) de l'utilisateur
     */

    public function getFavoriteMoviesFromUser($id) {
        // on construit la requête qui va récupérer les films de l'utilisateur
        $requete = "SELECT f.filmID, f.titre, p.commentaire, p.userID from film f" .
                " INNER JOIN prefere p ON f.filmID = p.filmID" .
                " AND p.userID = :userID";

        // on extrait le résultat de la BDD sous forme de tableau associatif
        $resultat = $this->extraireNxN($requete,
                ['userID' => $id]);
        // on crée les objets métiers
        $preferes = $this->buildPreferes($resultat);
        // on retourne le résultat
        return $preferes;
    }

    /*
     * Méthode qui renvoie les informations sur un film favori donné pour un utilisateur donné
     * @param int $userID Identifiant de l'utilisateur
     * @param int $filmID Identifiant du film
     * @return array[]
     */

    public function getFavoriteMovieInformations($userID, $filmID) {
        // requête qui récupère les informations d'une préférence de film pour un utilisateur donné
        $requete = "SELECT f.titre, p.userID, p.filmID, p.commentaire"
                . " FROM prefere p INNER JOIN film f ON p.filmID = f.filmID"
                . " WHERE p.userID = :userID AND p.filmID = :filmID";

        // on extrait les résultats de la BDD
        $resultat = $this->extraire1xN($requete,
                ['userID' => $userID,
            'filmID' => $filmID]);
        // on crée l'objet métier
        $prefere = $this->buildBusinessObject($resultat);
        // on retourne le résultat
        return $prefere;
    }

    /*
     * Méthode qui met à jour une préférence de film pour un utilisateur
     * @param int userID Identifiant de l'utilisateur
     * @param int filmID Identifiant du film
     * @param string comment Commentaire de l'utilisateur à propos de ce film
     */

    public function updateFavoriteMovie($userID, $filmID, $comment) {
        // on construit la requête d'insertion
        $requete = "UPDATE prefere SET commentaire = :comment"
                . " WHERE filmID = :filmID AND userID = :userID";
        // exécution de la requête
        $this->executeQuery($requete,
                ['userID' => $userID,
            'filmID' => $filmID,
            'comment' => $comment]);
    }

    /*
     * Méthode qui ajoute une préférence de film à un utilisateur
     * @param int userID Identifiant de l'utilisateur
     * @param int filmID Identifiant du film
     * @param string comment Commentaire de l'utilisateur à propos de ce film
     */

    public function insertNewFavoriteMovie($userID, $filmID, $comment = "") {
        // on construit la requête d'insertion
        $requete = "INSERT INTO prefere (filmID, userID, commentaire) VALUES ("
                . ":filmID"
                . ", :userID"
                . ", :comment)";

        // exécution de la requête
        $this->executeQuery($requete,
                ['filmID' => $filmID,
            'userID' => $userID,
            'comment' => $comment]);

        if ($this->logger) {
            $this->logger->info('Movie ' . $filmID . ' successfully added to ' . $userID . '\'s preferences.');
        }
    }

    public function deleteFavoriteMovie($userID, $filmID) {
        $this->executeQuery("DELETE FROM prefere"
                . " WHERE userID = :userID AND filmID = :filmID",
                ['userID' => $userID,
            'filmID' => $filmID]);

        if ($this->logger) {
            $this->logger->info('Movie ' . $filmID . ' successfully deleted from ' . $userID . '\'s preferences.');
        }
    }

}
