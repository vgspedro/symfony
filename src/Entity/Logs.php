<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="logs")
 * @ORM\Entity(repositoryClass="App\Repository\LogsRepository")
 */

class Logs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
    * @ORM\Column(type="datetime", name="datetime") */
    private $datetime;
    /**
    * @ORM\Column(type="text", name="log", nullable=true)
    */
    private $log;

    const STATUS_UPDATE = 'update';
    const STATUS_CREATE = 'create';
    const STATUS_DELETE = 'delete';
    
    /**
     * @Assert\Choice({"update", "create", "delete"})
     * @ORM\Column(type="string", name="status", columnDefinition="ENUM('update', 'create', 'delete')" )
     */
    private $status = self::STATUS_CREATE;

    public function getId()
    {
        return $this->id;
    }
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }


    public function getLog()
    {
        return $this->log;
    }

    public function setLog($log)
    {
        $this->log = $log;
    }

}
