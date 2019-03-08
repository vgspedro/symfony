<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="countries")
 * @ORM\Entity(repositoryClass="App\Repository\CountriesRepository")
 */
class Countries
{  
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /** @ORM\Column(type="string", length=2, name="iso")*/
    private $iso;
    /** @ORM\Column(type="string", length=50, name="name")*/
    private $name;
    /** @ORM\Column(type="string", length=50, name="nicename")*/
    private $nicename;
    /** @ORM\Column(type="string", length=3, name="iso3")*/
    private $iso3;
    /** @ORM\Column(type="integer", name="numcode")*/
    private $numcode;
    /** @ORM\Column(type="integer", name="phonecode")*/
    private $phonecode;

    public function getId() {
        return $this->id;
    }
    public function getIso() {
        return $this->iso;
    }
    public function getName() {
        return $this->name;
    }
    public function getNiceName() {
        return $this->nicename;
    }
    public function getIso3() {
        return $this->iso3;
    }
    public function getNumCode() {
        return $this->numcode;
    }
    public function getPhoneCode() {
        return $this->phonecode;
    }
}
