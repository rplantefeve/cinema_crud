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

    public function getCinema(): Cinema
    {
        return $this->cinema;
    }

    public function getFilm(): Film
    {
        return $this->film;
    }

    public function getHeureDebut(): DateTime
    {
        return $this->heureDebut;
    }

    public function getHeureFin(): DateTime
    {
        return $this->heureFin;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setCinema(Cinema $cinema): void
    {
        $this->cinema = $cinema;
    }

    public function setFilm(Film $film): void
    {
        $this->film = $film;
    }

    public function setHeureDebut(DateTime $heureDebut): void
    {
        $this->heureDebut = $heureDebut;
    }

    public function setHeureFin(DateTime $heureFin): void
    {
        $this->heureFin = $heureFin;
    }

    public function setVersion($version): void
    {
        $this->version = $version;
    }
}
