<?php

namespace Semeformation\Mvc\Cinema_crud\models;

class Utilisateur
{
    private $userId;

    private $nom;

    private $prenom;

    private $adresseCourriel;

    private $password;

    public function getUserId()
    {
        return $this->userId;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setAdresseCourriel($adresseCourriel): void
    {
        $this->adresseCourriel = $adresseCourriel;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }
}
