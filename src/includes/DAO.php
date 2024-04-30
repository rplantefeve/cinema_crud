<?php

namespace Semeformation\Mvc\Cinema_crud\includes;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

abstract class DAO
{
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
    public function __construct(
        Connection $connexion = null,
        LoggerInterface $logger = null
    ) {
        // init. de la connexion à la BDD
        $this->db = $connexion;
        // init. du logger
        $this->logger = $logger;
    }

    /**
     * Donne accès à la connexion à la BDD
     * @return Connection
     */
    protected function getDb()
    {
        return $this->db;
    }

    /**
     * Donne accès au logger du DAO
     * @return Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Méthode abstraite de construction d'un objet métier à partir d'une ligne de la BDD
     * Cette méthode DOIT être redéfinie dans les classes filles
     */
    abstract protected function buildBusinessObject($row);

    /**
     * Méthode abstraite de recherche d'un BO à partir de son id
     */
    abstract public function find(...$id);

    /**
     * Recherche tous les BO présents dans la BDD
     */
    abstract public function findAll();

    /**
     * Construit un tableau d'objets métiers à partir d'un résultat de BDD
     * @param type $rows
     * @return array
     */
    protected function buildBusinessObjects($rows)
    {
        $objets = [];
        foreach ($rows as $row) {
            $objets[] = $this->buildBusinessObject($row);
        }
        return ($objets ?? null);
    }

    /**
     * Extrait des résultats, un vecteur d'objets métiers
     * @param array|null $results
     * @return array<object>
     */
    protected function extractObjects($results): array
    {
        if ($results !== null) {
            // on crée les objets métiers
            $objects = $this->buildBusinessObjects($results);
            // on retourne le résultat
            return $objects;
        } else {
            return [];
        }
    }

}
