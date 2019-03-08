<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="currency")
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 */
class Currency
{  
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /** @ORM\Column(type="string", length=24, name="entity")*/
    private $entity;
    /** @ORM\Column(type="string", length=24, name="currency")*/
    private $currency;
    /** @ORM\Column(type="string", length=3, name="alphabetic_code")*/
    private $alphabetic_code;
    /** @ORM\Column(type="string", length=3, name="numeric")*/
    private $numeric;
    /** @ORM\Column(type="integer", name="minor")*/
    private $minor;

    public function getId() {
        return $this->id;
    }
    public function getEntity() {
        return $this->entity;
    }
    public function getCurrency() {
        return $this->currency;
    }
    public function getAlphabeticCode() {
        return $this->alphabetic_code;
    }
    public function getNumeric() {
        return $this->numeric;
    }
    public function getMinor() {
        return $this->minor;
    }
}
