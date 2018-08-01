<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */

class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /** @ORM\OneToOne(targetEntity="Client", mappedBy="booking", cascade={"persist"}) */
    private $client;
    /**
    * @Assert\NotBlank(message="TELEPHONE")
    */
    private $telephone;
    /**
     * @Assert\NotBlank(message="NAME")
     */
    private $name;
    /**
    * @Assert\NotBlank(message="EMAIL")
     */
    private $email;
    /**
    * @Assert\NotBlank(message="ADDRESS")
    */
    private $address;
    /**
    * @ORM\Column(type="integer", length=3, nullable=true)
    * @Assert\NotBlank(message="ADULT")
    */
    private $adult;
    /**
    * @ORM\Column(type="integer", length=3, nullable=true)
    * @Assert\NotBlank(message="CHILDREN")
    */
    private $children;
    /**
    * @ORM\Column(type="integer", length=3, nullable=true)
    * @Assert\NotBlank(message="BABY")
    */
    private $baby;

    /**
    * @ORM\Column(type="string", length=10)
    * @Assert\NotBlank(message="DATE")
    */
    private $date;
    /**
    * @ORM\Column(type="string", length=6)
    * @Assert\NotBlank(message="HOUR_TXT")
     */
    private $hour;
    /**
    * @ORM\OneToMany(targetEntity="Category", mappedBy="booking")
    * @ORM\Column(name="tourtype",type="string", length=50)
    * @Assert\NotBlank(message="TOUR_TXT")
    */
    private $tourtype;

     /**
     * @ORM\Column(type="string", length=250)
     */
    private $message;

     /** @ORM\Column(name="posted_at", type="datetime") */
    private $postedAt;

    /** @ORM\Column(type="text", name="notes", nullable=true ) */
    private $notes;

    /** @ORM\Column(type="string", name="status", nullable=true) */
    private $status = 'PENDING';
    
    /**
    * @Assert\NotBlank(message="RGPD")
    */
    private $rgpd;

    private $language;

 	public function getId()
    {
        return $this->id;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getLanguage()
    {
        return $this->language;
    }


    public function setLanguage($language)
    {
        $this->language = $language;
    }


    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }
    
    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getAdult()
    {
        return $this->adult;
    }

    public function setAdult($adult)
    {
        $this->adult = $adult;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getBaby()
    {
        return $this->baby;
    }

    public function setBaby($baby)
    {
        $this->baby = $baby;
    }

    public function getDate()
    {
        return $this->date;
    }

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

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

 	public function getPostedAt()
    {
        return $this->postedAt;
    }

    public function setPostedAt($postedAt)
    {
        $this->postedAt = $postedAt;
    }

    public function getRgpd()
    {
        return $this->rgpd;
    }

    public function setRgpd($rgpd)
    {
        $this->rgpd = $rgpd;
    }

}
