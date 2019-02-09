<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="available")
 * @ORM\Entity(repositoryClass="App\Repository\AvailableRepository")
 */

class Available
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
    * @ORM\Column(type="datetime") */
    private $datetime;
    /** 
     * @Assert\NotBlank()
     * @Assert\Type("Category")
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="booking") 
     */
    private $category;
    /**
    * @ORM\Column(type="integer", name="stock", nullable=true)
    */
    private $stock;


    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }

    /*
    must receive the date and hour of the booking
    */
    public function setDate($date)
    {

        $this->date = $date;
    }

    public function getHour()
    {
        return $this->hour;
    }

    public function setHour($hour)
    {
        $this->hour = $hour;
    }

    public function getTourType()
    {
        return $this->tourtype;
    }

    public function setTourType($tourtype)
    {
        $this->tourtype = $tourtype;
    }


    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($stock)
    {

        $this->stock = $stock;
    }

}
