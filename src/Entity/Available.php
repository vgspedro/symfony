<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="available") 
     */
    private $category;
    /**
    * @ORM\Column(type="integer", name="stock", nullable=true)
    */
    private $stock;
    /**
    * @ORM\Column(type="integer", name="lotation", nullable=true)
    */
    private $lotation;

    public function getId()
    {
        return $this->id;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    /*
    must receive the date and hour of the booking
    */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    public function getHour()
    {
        return $this->hour;
    }

    public function setHour($hour)
    {
        $this->hour = $hour;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category)
    {
        $this->category = $category;
    }


    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($stock)
    {

        $this->stock = $stock;
    }

    public function getLotation()
    {
        return $this->lotation;
    }

    public function setLotation($lotation)
    {

        $this->lotation = $lotation;
    }

}
