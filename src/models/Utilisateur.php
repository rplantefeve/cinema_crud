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

    public function getAdresseCourriel()
    {
        return $this->adresseCourriel;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function setAdresseCourriel($adresseCourriel)
    {
        $this->adresseCourriel = $adresseCourriel;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

}
