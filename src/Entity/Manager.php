<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ManagerRepository")
 */
class Manager extends User
{  

   /** 
     * @ORM\Column(type="integer", name="nif", nullable=true)
     */
    private $nif;  

    /** 
     * @ORM\Column(type="text", name="address", nullable=true)
     */
    private $address;
    
    /** 
     * @ORM\Column(type="text", name="city", nullable=true)
     */
    private $city;
    
    /**
     * @ORM\Column(name="postal_code", length=9, type="text", nullable=true)
     */
    private $postal_code;


    /**
     * @ORM\Column(name="phone", type="text")
     */
    private $phone;


    public function getType()
    {
        return 'manager';    
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }
  
    public function getNif() {
            return $this->nif;
    }

    public function setNif($nif) {
        $this->nif = $nif;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }
    
    public function getPostalCode() {
        return $this->postal_code;
    }
    
    public function setPostalCode($postal_code) {
        $this->postal_code = $postal_code;
    }
  
    public function getRoles()
    {
        return ['ROLE_MANAGER'];    
    }
    
}
