<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Money\Money;


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
     * @ORM\Column(type="string", length=1000)
     */
    private $descriptionPt;

    /**
    * @Assert\NotBlank(message="DESCRIPTION_EN")
     * @ORM\Column(type="string", length=1000)
     */
    private $descriptionEn;

    /**
     * @ORM\Column(type="string", length=1000, name="warranty_payment_pt", nullable=true)
     */
    private $warrantyPaymentPt;
    /**
     * @ORM\Column(type="string", length=1000, name="warranty_payment_en", nullable=true)
     */
    private $warrantyPaymentEn;
    /**
    * @Assert\NotBlank(message="CHILDREN_PRICE")
    * @ORM\Column(type="money", options={"unsigned"=true}) 
    */
    private $childrenPrice;
    /**
    * @Assert\NotBlank(message="ADULT_PRICE")
    * @ORM\Column(type="money", options={"unsigned"=true})
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
    private $highlight = false;
    
    /** @ORM\Column(type="boolean", name="warranty_payment", options={"default":0}) */
    private $warrantyPayment = false;
    
    /** @ORM\OneToMany(targetEntity="Available", mappedBy="category") */
    private $available;

     /** @ORM\Column(type="string", length=5, name="duration", options={"default":"00:00"})*/
    private $duration; 

    /** @ORM\Column(type="integer", name="order_by", nullable=true)*/
    private $orderBy;

    /** 
     * @ORM\Column( name="deposit", type="decimal", precision=2, scale=2, options={"default":"0.00"}) 
     */
    private $deposit;


    public function __construct()
    {   
        $this->available = new ArrayCollection();
        $this->event = new ArrayCollection();
        $this->blockdate = new ArrayCollection();
        $this->deposit = "0.00";

    }

    public function getDeposit() {
        return $this->deposit;
    }

    public function setDeposit($deposit) {
        $this->deposit = $deposit;
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
        $this->warrantyPaymentPt = str_replace("'","’",$warrantyPaymentPt);
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }

    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    public function getWarrantyPaymentEn()
    {
        return $this->warrantyPaymentEn;
    }

    public function setWarrantyPaymentEn($warrantyPaymentEn)
    {
        $this->warrantyPaymentEn = str_replace("'","’",$warrantyPaymentEn);
    }

    public function getAvailability()
    {
        return $this->availability;
    }

    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
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
        $this->namePt = str_replace("'","’",$namePt);
    }

    public function getNameEn()
    {
        return $this->nameEn;
    }

    public function setNameEn($nameEn)
    {
        $this->nameEn = str_replace("'","’",$nameEn);
    }

    public function getDescriptionPt()
    {
        return $this->descriptionPt;
    }

    public function setDescriptionPt($descriptionPt)
    {
        $this->descriptionPt = str_replace("'","’",$descriptionPt);
    }

    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn($descriptionEn)
    {
        $this->descriptionEn = str_replace("'","’",$descriptionEn);
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

    public function getAvailable()
    {
        return $this->available;
    }

    public function setAvailable(Available $available)
    {
        $this->available = $available;
    }
    
    public function addAvailable(Available $available)
    {
        $available->setCategory($this);
        $this->available->add($available);
    }
    
    public function removeAvailable(Available $available)
    {
        $this->available->removeElement($available);
    }

    /** 
     * @return \Money\Money
     */
    public function getAdultPrice()
    {
        return $this->adultPrice;
    }

    public function setAdultPrice(Money $adultPrice)
    {
        $this->adultPrice = $adultPrice;
    }

    /** 
     * @return \Money\Money
    */
    public function getChildrenPrice()
    {
        return $this->childrenPrice;
    }

    public function setChildrenPrice(Money $childrenPrice)
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