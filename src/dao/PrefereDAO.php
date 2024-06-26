<?php

namespace Semeformation\Mvc\Cinema_crud\dao;

use Semeformation\Mvc\Cinema_crud\includes\DAO;
use Semeformation\Mvc\Cinema_crud\models\Prefere;
use Semeformation\Mvc\Cinema_crud\dao\FilmDAO;
use Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO;
use Semeformation\Mvc\Cinema_crud\exceptions\BusinessObjectDoNotExist;

/**
 * Description of PrefereDAO
 *
 * @author User
 */
class PrefereDAO extends DAO
{
    /**
     * DAO Film
     * @var \Semeformation\Mvc\Cinema_crud\dao\FilmDAO
     */
    private $filmDAO;

    /**
     * DAO Utilisateur
     * @var \Semeformation\Mvc\Cinema_crud\dao\UtilisateurDAO;
     */
    private $utilisateurDAO;

    /**
     * Crée une préférence à partir d'une ligne de la BDD.
     *
     * @param array $row La ligne de résultat de la BDD.
     * @return Prefere
     */
    protected function buildBusinessObject($row)
    {
        $prefere = new Prefere();
        $prefere->setCommentaire($row['COMMENTAIRE']);
        // trouver l'utilisateur concerné grâce à son identifiant
        if (array_key_exists('USERID', $row) === true) {
            $userId = $row['USERID'];
            $utilisateur = $this->utilisateurDAO->find($userId);
            $prefere->setUtilisateur($utilisateur);
        }
        // trouver le film concerné grâce à son identifiant
        if (array_key_exists('FILMID', $row) === true) {
            $filmId = $row['FILMID'];
            $film = $this->filmDAO->find($filmId);
            $prefere->setFilm($film);
        }
        // on retourne l'objet métier ainsi "hydraté"
        return $prefere;
    }

    /**
     * Renvoie les informations sur un film favori donné pour un utilisateur donné
     * @param array<string> $id
     * @return Prefere
     * @throws \Exception
     */
    public function find(...$id)
    {
        // requête qui récupère les informations d'une préférence de film pour un utilisateur donné
        $requete = "SELECT f.TITRE, p.*"
                . " FROM prefere p INNER JOIN film f ON p.filmID = f.filmID"
                . " WHERE p.userID = ? AND p.filmID = ?";
        $resultat = $this->getDb()->fetchAssoc(
            $requete,
            [
                $id[0],
                $id[1],
            ]
        );
        // si trouvé
        if ($resultat !== false) {
            // on récupère et on retourne l'objet préférence
            return $this->buildBusinessObject($resultat);
        } else {
            throw new BusinessObjectDoNotExist("Aucune préférence trouvée pour l'utilisateur d'id=" . $id[0] . " pour le film d'id=" . $id[1]);
        }
    }

    /**
     * Retourne toutes les préférences de films
     *
     * @return array<Prefere>
     */
    public function findAll()
    {
        // requête d'extraction de toutes les préférences
        $sql = "SELECT * FROM prefere";
        $resultats = $this->getDb()->fetchAll($sql);

        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Retourne les films préférés d'un utilisateur donné à partir de l'id
     *
     * @param int $id Identifiant de l'utilisateur
     * @return array<Prefere> Les films préférés (sous forme de tableau associatif) de l'utilisateur
     */
    public function findAllByUserId(int $id): array
    {
        // on construit la requête qui va récupérer les films de l'utilisateur
        $requete = "SELECT f.FILMID, f.TITRE, p.COMMENTAIRE, p.USERID from film f" .
                " INNER JOIN prefere p ON f.filmID = p.filmID" .
                " AND p.userID = :userID"
                . " ORDER BY f.TITRE ASC";

        // on extrait le résultat de la BDD sous forme de tableau associatif
        $resultats = $this->getDb()->fetchAll(
            $requete,
            ['userID' => $id]
        );
        // on extrait les objets métiers des résultats
        return $this->extractObjects($resultats);
    }

    /**
     * Sauvegarde un objet Prefere en BDD
     * @param Prefere $prefere
     */
    public function save(Prefere $prefere)
    {
        // je récupère les données de l'objet métier sous forme de tableau
        $donneesPrefere = [
            'filmId'      => $prefere->getFilm()->getFilmId(),
            'userId'      => $prefere->getUtilisateur()->getUserId(),
            'commentaire' => $prefere->getCommentaire(),
        ];

        try {
            // la préférence existe-t-elle ?
            $this->find(
                $donneesPrefere['userId'],
                $donneesPrefere['filmId']
            );
            // il faut faire une mise à jour
            $this->getDb()->update(
                'prefere',
                $donneesPrefere,
                [
                    'filmId' => $prefere->getFilm()->getFilmId(),
                    'userId' => $prefere->getUtilisateur()->getUserId(),
                ]
            );
        } catch (BusinessObjectDoNotExist $e) {
            // Sinon, nous faisons une insertion
            $this->getDb()->insert('prefere', $donneesPrefere);
        }
    }

    /**
     * Supprime une préférence de film
     * @param string $userID
     * @param string $filmID
     */
    public function delete($userID, $filmID)
    {
        $this->getDb()->delete(
            'prefere',
            [
                'userId' => $userID,
                'filmId' => $filmID,
            ]
        );
    }

    public function getFilmDAO()
    {
        return $this->filmDAO;
    }

    public function getUtilisateurDAO()
    {
        return $this->utilisateurDAO;
    }

    public function setFilmDAO(FilmDAO $filmDAO)
    {
        $this->filmDAO = $filmDAO;
    }

    public function setUtilisateurDAO(UtilisateurDAO $utilisateurDAO)
    {
        $this->utilisateurDAO = $utilisateurDAO;
    }
}
