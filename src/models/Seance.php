<?php

namespace Semeformation\Mvc\Cinema_crud\models;

use Semeformation\Mvc\Cinema_crud\models\Cinema;
use Semeformation\Mvc\Cinema_crud\models\Film;
use DateTime;

/**
 * Description of Seance
 *
 * @author User
 */
class Seance
{
    /**
     *
     * @var Cinema
     */
    private $cinema;

    /**
     *
     * @var Film
     */
    private $film;

    /**
     *
     * @var DateTime
     */
    private $heureDebut;

    /**
     *
     * @var DateTime
     */
    private $heureFin;

    /**
     *
     * @var string
     */
    private $version;

    public function getCinema()
    {
        return $this->cinema;
    }

    public function getFilm()
    {
        return $this->film;
    }

    public function getHeureDebut()
    {
        return $this->heureDebut;
    }

    public function getHeureFin()
    {
        return $this->heureFin;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setCinema(Cinema $cinema)
    {
        $this->cinema = $cinema;
    }

    public function setFilm(Film $film)
    {
        $this->film = $film;
    }

    public function setHeureDebut(DateTime $heureDebut)
    {
        $this->heureDebut = $heureDebut;
    }

    public function setHeureFin(DateTime $heureFin)
    {
        $this->heureFin = $heureFin;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }
}
