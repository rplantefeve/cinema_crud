<?php

namespace Semeformation\Mvc\Cinema_crud\includes;

use Doctrine\DBAL\Connection;
use Semeformation\Mvc\Cinema_crud\includes\DBFactory;
use Semeformation\Mvc\Cinema_crud\includes\Utils;
use Psr\Log\LoggerInterface;
use PDO;

abstract class DAO {

    /**
     * Connexion à la BDD
     * @var Doctrine\DBAL\Connection 
     */
    private $db;

    /**
     * Logger du DAO
     * @var Psr\Log\LoggerInterface 
     */
    protected $logger;

    /**
     * Constructeur de la classe DAO
     * @param Connection $connexion
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $connexion = null,
            LoggerInterface $logger = null) {
        // init. de la connexion à la BDD
        $this->db     = $connexion;
        // init. du logger
        $this->logger = $logger;
    }

    /**
     * Donne accès à la connexion à la BDD
     * @return Connection
     */
    protected function getDb(): \Doctrine\DBAL\Connection {
        return $this->db;
    }

    /**
     * Donne accès au logger du DAO
     * @return Psr\Log\LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * Méthode abstraite de construction d'un objet métier à partir d'une ligne de la BDD
     * Cette méthode DOIT être redéfinie dans les classes filles
     */
    protected abstract function buildBusinessObject($row);

    /**
     * Méthode abstraite de recherche d'un BO à partir de son id
     */
    public abstract function find($id);

    /**
     * Recherche tous les BO présents dans la BDD
     */
    public abstract function findAll();

    /**
     * Construit un tableau d'objets métiers à partir d'un résultat de BDD
     * @param type $rows
     * @return array
     */
    protected function buildBusinessObjects($rows) {
        foreach ($rows as $row) {
            $objets[] = $this->buildBusinessObject($row);
        }
        return $objets;
    }

    /**
     * Extrait des résultats, un vecteur d'objets métiers
     * @param type $results
     * @return Object[]
     */
    protected function extractObjects($results) {
        if (!is_null($results)) {
            // on crée les objets métiers
            $objects = $this->buildBusinessObjects($results);
            // on retourne le résultat
            return $objects;
        } else {
            return array();
        }
    }

    /**
     * Exécute une requête SQL
     *
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @return PDOStatement Résultats de la requête
     */
    public function executeQuery($sql, $params = null) {
        // si pas de paramètres
        if (is_null($params)) {
            // exécution directe
            $resultat = DBFactory::getFactory($this->logger)->getConnection()->query($sql);
        } else {
            // requête préparée
            $resultat = DBFactory::getFactory($this->logger)->getConnection()->prepare($sql);
            $resultat->execute($params);
        }
        if ($this->logger) {
            $this->logger->debug('Query successfully executed : ' . $sql);
        }
        return $resultat;
    }

    /*
     * Fonctions utilitaires
     */

    /**
     * Retourne les lignes d'enregistrements sous forme de tableau associatif
     * Ici, on aura N lignes, N colonnes
     * @param string $unSQLSelect La requête SQL
     * @param array $parametres Les éventuels paramètres de la requête
     * @param boolean $estVisible (visualisation du résultat)
     * @return array[][] ou null
     */
    protected function extraireNxN($unSQLSelect, $parametres = null,
            $estVisible = false) {
        // tableau des résultats
        $tableau  = array();
        // résultat de la requête
        $resultat = $this->executeQuery($unSQLSelect, $parametres);

        // boucle de construction du tableau de résultats
        while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
            $tableau[] = $ligne;
        }
        unset($resultat);

        // si la tableau ne contient pas d'élément
        if (count($tableau) == 0) {
            $tableau = null;
        }

        // si l'on souhaite afficher le contenu du tableau (DEBUG MODE)
        if ($estVisible) {
            Utils::afficherResultat($tableau, $unSQLSelect);
        }

        // on retourne le tableau de résultats
        return $tableau;
    }

    /**
     * Retourne une ligne d'enregistrement sous forme de tableau associatif
     * @param string $unSQLSelect
     * @param array $parametres Tableau des paramètres de la requête
     * @param boolean $estVisible (visualisation du résultat)
     * @return array[] ou null
     */
    protected function extraire1xN($unSQLSelect, $parametres = null,
            $estVisible = false) {
        $result = $this->extraireNxN($unSQLSelect, $parametres, false);
        if (isset($result[0])) {
            $result = $result[0];
        }
        if ($estVisible) {
            Utils::afficherResultat($result, $unSQLSelect);
        }
        return $result;
    }

}
