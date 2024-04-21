<?php

namespace Semeformation\Mvc\Cinema_crud\models;

/**
 * Description of Film
 *
 * @author User
 */
class Film
{
    private $filmId;
    private $titre;
    private $titreOriginal;

    public function getFilmId()
    {
        return $this->filmId;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getTitreOriginal()
    {
        return $this->titreOriginal;
    }

    public function setFilmId($filmId)
    {
        $this->filmId = $filmId;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setTitreOriginal($titreOriginal)
    {
        $this->titreOriginal = $titreOriginal;
    }
}
