<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */

class Event
{

    //public function __toString() {
    //$this->category;        // return $this->event;
    //return (string) $this->category->getId();
    //}
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /** @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="event") */
    private $category;
    /**
    * @ORM\Column(type="string", name="event", length=150)
    */
    private $event;

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

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event)
    {
        $this->event = $event;
    }
}