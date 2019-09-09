<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(name="stripe_payment_logs")
* @ORM\Entity(repositoryClass="App\Repository\StripePaymentLogsRepository")
*/
class StripePaymentLogs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;     
    /** 
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="stripe_payment_logs") 
     */
    private $booking;
    /**
     * @ORM\Column(type="text", name="log")
     */
    private $log;    
   	   	   
	public function getId(){
		return $this->id;
	}
	
	public function getLog(){
		return $this->log;
	}

	public function setBooking(Booking $booking) {
		$this->booking = $booking;
	}

	public function getBooking() {
		return $this->booking;
	}

	public function setLog($log){
		$this->log = trim($log);
	}

    public function getLogObj() {
        $obj = null;

        if ($this->log){
            //TEXT TO OBJECT
            $obj = json_decode($this->log);
        }
        return $obj;
    }

}
