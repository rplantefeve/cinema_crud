<?php

namespace Semeformation\Mvc\Cinema_crud\includes;

use PDO;
use Psr\Log\LoggerInterface;

class DBFactory
{
    // logger
    private $logger;
    // instance unique de la classe (singleton)
    private static $factory;
    // instance de la classe PDO
    private $pdoInstance = null;
    // Champs de connexion à la BDD
    private $user = "user";
    private $pass = "Us3rUs€r";
    private $dataSourceName = "mysql:host=127.0.0.1;dbname=cinema_crud;charset=utf8";

    /*
     * Méthode utile pour récupérer le singleton depuis le programme appelant
     */

    public static function getFactory(LoggerInterface $logger = null)
    {
        // si l'instance n'a encore jamais été instanciée
        if (!self::$factory) {
            self::$factory = new DBFactory($logger);
        }
        // on retourne l'instance de la classe
        return self::$factory;
    }

    /*
     * Constructeur de la classe qui initialise la connexion
     */

    public function getConnection()
    {
        if (!$this->pdoInstance) {
            // on appelle le constructeur de la classe PDO
            $this->pdoInstance = new PDO(
                $this->dataSourceName,
                $this->user,
                $this->pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            if ($this->logger) {
                $this->logger->info('Database connection succeeded.');
            }
        }
        return $this->pdoInstance;
    }

    private function __construct(LoggerInterface $logger = null)
    {
        if ($logger) {
            $this->logger = $logger;
        }
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone()
    {
        trigger_error(
            'Clone is not allowed.',
            E_USER_ERROR
        );
    }

    public function __wakeup()
    {
        trigger_error(
            'Deserializing is not allowed.',
            E_USER_ERROR
        );
    }
}
