<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Table(name="extra_payment")
* @ORM\Entity(repositoryClass="App\Repository\ExtraPaymentRepository")
*/
class ExtraPayment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
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

	public function setLog($log){
		$this->log = trim($log);
	}

    public function getLogObj() {
        $obj = '';
        if ($this->getLog()){
            $obj = json_decode($this->getLog());
        }
        return $obj;
    }
    
    public function getObjectType(){
        if ($this->getLogObj()){
            $obj = json_decode($this->getLog());
            if($obj->object == 'payment_intent')
                $type = 'payment_intent';
            else if ($obj->object == 'charge')
                $type = 'charge';
        }
        return $type;
    }

}
