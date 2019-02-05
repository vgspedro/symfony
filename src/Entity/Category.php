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
     * @ORM\Column(type="string", length=350, name="warranty_payment_pt")
     */
    private $warrantyPaymentPt;
    /**
     * @ORM\Column(type="string", length=350, name="warranty_payment_en")
     */
    private $warrantyPaymentEn;
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

    /** @ORM\Column(type="integer", name="availability")*/
    private $availability;

    /** @ORM\Column(type="boolean", name="highlight", options={"default":0}) */
    private $highlight;
    
    /** @ORM\Column(type="boolean", name="warranty_payment", options={"default":0}) */
    private $warrantyPayment;

    public function __construct()
    {   
        $this->event = new ArrayCollection();
        $this->blockdate = new ArrayCollection();      
    }


    public function getWarrantyPayment()
    {
        return $this->warrantyPayment;
    }

    public function setWarrantyPayment($warrantyPayment)
    {
        $this->warrantyPayment = $warrantyPayment;
    }

    public function getWarrantyPaymentPt()
    {
        return $this->warrantyPaymentPt;
    }

    public function setWarrantyPaymentPt($warrantyPaymentPt)
    {
        $this->warrantyPaymentPt = $warrantyPaymentPt;
    }

    public function getWarrantyPaymentEn()
    {
        return $this->warrantyPaymentEn;
    }

    public function setWarrantyPaymentEn($warrantyPaymentEn)
    {
        $this->warrantyPaymentEn = $warrantyPaymentEn;
    }

    public function getAvailability()
    {
        return $this->availability;
    }

    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }


    public function getHighlight()
    {
        return $this->highlight;
    }

    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
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