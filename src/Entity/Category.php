<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */

class Category
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="NAME_PT")
     */
    private $namePt;
    /**
     * @Assert\NotBlank(message="NAME_EN")
     * @ORM\Column(type="string", length=50)
     */
    private $nameEn;
    /**
     * @Assert\NotBlank(message="DESCRIPTION_PT")
     * @ORM\Column(type="string", length=350)
     */
    private $descriptionPt;

    /**
    * @Assert\NotBlank(message="DESCRIPTION_EN")
     * @ORM\Column(type="string", length=350)
     */
    private $descriptionEn;

    /**
    * @Assert\NotBlank(message="CHILDREN_PRICE")
     * @ORM\Column(type="decimal", scale=2)
     */
    private $childrenPrice;
    /**
    * @Assert\NotBlank(message="ADULT_PRICE")
     * @ORM\Column(type="decimal", scale=2)
     */
    private $adultPrice;
    
    /** @ORM\OneToMany(targetEntity="Blockdates", mappedBy="category", cascade={"persist"}) */
    private $blockdate;
    
    /** @ORM\OneToMany(targetEntity="Event", mappedBy="category", cascade={"persist"}) */
    private $event;

    /** @ORM\Column(type="boolean", name="is_active", options={"default":0}) */
    private $isActive;

    /**
     * @ORM\Column(type="string", name="image")
     * @Assert\File(mimeTypes={"image/gif", "image/png", "image/jpeg"})
     */
    private $image;


    public function getImage()
    {
        return $this->image;
    }

    public function setimage($image)
    {
        $this->image = $image;

        return $this;
    }


    public function __construct()
    {   
        $this->event = new ArrayCollection();
        $this->blockdate = new ArrayCollection();      
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNamePt()
    {
        return $this->namePt;
    }

    public function setNamePt($namePt)
    {
        $this->namePt = $namePt;
    }

    public function getNameEn()
    {
        return $this->nameEn;
    }

    public function setNameEn($nameEn)
    {
        $this->nameEn = $nameEn;
    }

    public function getDescriptionPt()
    {
        return $this->descriptionPt;
    }

    public function setDescriptionPt($descriptionPt)
    {
        $this->descriptionPt = $descriptionPt;
    }

    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = $descriptionEn;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }
    
    public function addEvent(Event $event)
    {
        $event->setCategory($this);
        $this->event->add($event);
    }
    
    public function removeEvent(Event $event)
    {
        $this->event->removeElement($event);
    }

    public function getAdultPrice()
    {
        return $this->adultPrice;
    }

    public function setAdultPrice($adultPrice)
    {
        $this->adultPrice = $adultPrice;
    }

    public function getChildrenPrice()
    {
        return $this->childrenPrice;
    }

    public function setChildrenPrice($childrenPrice)
    {
        $this->childrenPrice = $childrenPrice;
    }


    public function getBlockDate()
    {
        return $this->blockdate;
    }

    public function setBlockDate(Blockdates $blockdate)
    {
        $this->blockdate = $blockdate;
    }
    
    public function addBlockDate(Blockdates $blockdate)
    {
        $blockdate->setCategory($this);
        $this->blockdate->add($blockdate);
    }
    
    public function removeBlockDate(Blockdates $blockdate)
    {
        $this->blockdate->removeElement($blockdate);
    }

}