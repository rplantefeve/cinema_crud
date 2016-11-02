<?php

namespace Semeformation\Mvc\Cinema_crud\includes;

use PDO;

/*
 * Classe de connexion à la BDD. C'est elle qui va fournir à qui mieux-mieux
 * une connexion à la BDD afin de l'interroger.
 */
class DBFactory {

    // instance unique de la classe (singleton)
    private static $factory;
    // instance de la classe PDO
    private $pdoInstance = null;
    // Champs de connexion à la BDD
    private $user = "userCinema";
    private $pass = "pwdCinema";
    private $dataSourceName = "mysql:host=127.0.0.1;dbname=cinema_crud;charset=utf8";

    /*
     * Méthode utile pour récupérer le singleton depuis le programme appelant
     */

    public static function getFactory() {
        // si l'instance n'a encore jamais été instanciée
        if (!self::$factory) {
            self::$factory = new DBFactory();
        }
        // on retourne l'instance de la classe
        return self::$factory;
    }

    /*
     * Constructeur de la classe qui initialise la connexion
     */

    public function getConnection() {
        if (!$this->pdoInstance) {
            // on appelle le constructeur de la classe PDO
            $this->pdoInstance = new PDO($this->dataSourceName,
                    $this->user,
                    $this->pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return $this->pdoInstance;
    }

    private function __construct() {
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.',
                E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Deserializing is not allowed.',
                E_USER_ERROR);
    }

}
