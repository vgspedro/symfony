<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
{  
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /** @ORM\Column(type="text", name="name")*/
    private $name;

    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
}
