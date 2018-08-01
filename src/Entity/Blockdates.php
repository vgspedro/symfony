<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Table(name="blockdate")
 * @ORM\Entity(repositoryClass="App\Repository\BlockdatesRepository")
 */

class Blockdates
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
    *@ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="blockdate")
    */
    private $category;
    
    /**
     * @ORM\Column(type="string", name="blockdate", length=150, nullable=true)
     */
    private $date;

    /**
    * @ORM\Column(type="boolean", name="charge_total", options={"default": 1}) 
    */
    private $onlyDates;


    public function __toString() {
        return (string) $this->category->getId();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category) {
        $this->category = $category;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getOnlyDates()
    {
        return $this->onlyDates;
    }

    public function setOnlyDates($onlyDates)
    {
        $this->onlyDates = $onlyDates;
    }
}