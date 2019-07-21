<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="App\Repository\FeedbackRepository")
 */

class Feedback
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /** @ORM\ManyToOne(targetEntity="Booking") */
    private $booking;
    /**
    * @ORM\Column(type="integer", name="rate")
    */
    private $rate;
    /**
    * @ORM\Column(type="text", name="observations")
    */
    private $observations;

    /**
    * @ORM\Column(type="boolean", name="visible", options={"default":0})
     */
    private $visible;

    /**
    * @ORM\Column(type="boolean", name="active", options={"default":0})
     */
    private $active;

    public function getId()
    {
        return $this->id;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getBooking()
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking) {
        $this->booking = $booking;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    public function getObservations()
    {
        return $this->observations;
    }

    public function setObservations($observations)
    {
        $this->observations = $observations;
    }

    public function getCategory(){
        $category = null;
        if($this->getBooking())
            $category = $this->getBooking()->getAvailable()->getId();
        return $category;
    }

}