<?php

namespace Semeformation\Mvc\Cinema_crud\models;

/**
 * Description of Cinema
 *
 * @author User
 */
class Cinema {

    private $cinemaId;
    private $denomination;
    private $adresse;

    public function getCinemaId() {
        return $this->cinemaId;
    }

    public function getDenomination() {
        return $this->denomination;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function setCinemaId($cinemaId) {
        $this->cinemaId = $cinemaId;
    }

    public function setDenomination($denomination) {
        $this->denomination = $denomination;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

}
