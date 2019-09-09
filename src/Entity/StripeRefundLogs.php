<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(name="stripe_refund_logs")
* @ORM\Entity(repositoryClass="App\Repository\StripeRefundLogsRepository")
*/
class StripeRefundLogs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;     
    /** 
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="stripe_refund_logs") 
     */
    private $booking;
    /**
     * @ORM\Column(type="text", name="log")
     */
    private $log;    
   	   	   
	public function getId() {
		return $this->id;
	}
	
	public function getLog() {
		return $this->log;
	}

	public function setBooking(Booking $booking) {
		$this->booking = $booking;
	}

	public function getBooking() {
		return $this->booking;
	}

    public function getLogObj() {
        $obj = array();
        if ($this->log){
            $a = explode('Stripe\Refund JSON:', $this->log);
                //start at 1 else index 0 is null
                for ($i = 1; $i < count($a); $i++){
                    $txt = str_replace("Stripe\Refund JSON:", "", $a[$i]);
                    //TEXT TO OBJECT
                    $obj[] = json_decode($a[$i]);
            }
        }
        return $obj;
    }

    public function setLog($log) {
        $this->log = trim($log);
    }




}
