<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Money\Money;

/**
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */

class Booking
{
    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_CANCELED = 'canceled';
    const STATUS_CLEARED = 'cleared';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DENIED = 'denied';
    const STATUS_FAILED = 'failed';
    const STATUS_HELD = 'held';
    const STATUS_PAID = 'paid';
    const STATUS_PLACED = 'placed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_REFUSED = 'refused';
    const STATUS_REMOVED = 'removed';
    const STATUS_RETURNED = 'returned';
    const STATUS_REVERSED = 'reversed';
    const STATUS_UNCLAIMED = 'unclaimed';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELED_BY_USER = 'canceled by user';
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_PARTIAL_REFUND = 'partial_refund';
    const STATUS_UNCAPTURED = 'uncaptured';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="Client", cascade={"persist"}) */
    private $client;
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
     * @Assert\NotBlank()
     * @Assert\Type("Available")
     * @ORM\ManyToOne(targetEntity="Available") 
     */
    private $available;

    /** @ORM\ManyToOne(targetEntity="StripePaymentLogs", cascade={"persist"}) */
    private $stripePaymentLogs;

    /** @ORM\Column(name="date_event", type="date") */
    private $dateEvent;

    /** @ORM\Column(name="time_event", type="time") */
    private $timeEvent;
     /** @ORM\Column(name="posted_at", type="date") */
    private $postedAt;

    /** @ORM\Column(type="text", name="notes", nullable=true ) */
    private $notes;
    /**
     * @Assert\Choice({"pending", "canceled", "confirmed"})
     * @ORM\Column(type="string", name="status", columnDefinition="ENUM('pending', 'canceled', 'confirmed')" )
     */
    private $status = self::STATUS_PENDING;
    /**
     * @Assert\Choice({"incomplete", "canceled", "cleared", "completed", "denied", "failed", "held", "paid", "placed", "processing", "refunded", "refused", "removed", "returned", "reversed", "unclaimed", "approved", "canceled by user", "pending", "succeeded", "partial_refund", "uncaptured"})
     * @ORM\Column(type="string", name="payment_status", columnDefinition="ENUM('incomplete', 'canceled', 'cleared', 'completed', 'denied', 'failed', 'held', 'paid', 'placed', 'processing', 'refunded', 'refused', 'removed', 'returned', 'reversed', 'unclaimed', 'approved', 'canceled by user', 'pending', 'succeeded', 'partial_refund', 'uncaptured')" )
     */
    private $paymentStatus = null;

    /** @ORM\Column(type="money", name="amount", options={"unsigned"=true}) */
    private $amount;

    /** @ORM\Column(type="money", name="deposit_amount", options={"unsigned"=true}) */
    private $depositAmount = 0;

    public function setAmount(Money $amount) {
        $this->amount = $amount;
    }
    /** 
     * @return \Money\Money
     */
    public function getAmount() {
        return $this->amount;
    }

    public function setDepositAmount(Money $depositAmount) {
        $this->depositAmount = $depositAmount;
    }
    /** 
     * @return \Money\Money
     */
    public function getDepositAmount() {
        return $this->depositAmount;
    }

 	public function getId()
    {
        return $this->id;
    }

    public function setDateEvent($dateEvent)
    {
        $this->dateEvent = $dateEvent;
    }

    public function getDateEvent()
    {
        return $this->dateEvent;
    }

  public function setTimeEvent($timeEvent)
    {
        $this->timeEvent = $timeEvent;
    }

    public function getTimeEvent()
    {
        return $this->timeEvent;
    }

    public function setStripePaymentLogs(StripePaymentLogs $stripePaymentLogs)
    {
        $this->stripePaymentLogs = $stripePaymentLogs;
    }

    public function getStripePaymentLogs()
    {
        return $this->stripePaymentLogs;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setAvailable(Available $available)
    {
        $this->available = $available;
    }

    public function getAvailable()
    {
        return $this->available;
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

    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    public function getAdult()
    {
        return $this->adult;
    }

    public function setAdult($adult)
    {
        $this->adult = $adult;
    }

    //sum the total of persons of booking
    public function getCountPax()
    {
        $total = 0;
        $total = (int)$this->getAdult() + (int)$this->getChildren() + (int)$this->getBaby();
        return $total;
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

 	public function getPostedAt()
    {
        return $this->postedAt;
    }

    public function setPostedAt($postedAt)
    {
        $this->postedAt = $postedAt;
    }
}
