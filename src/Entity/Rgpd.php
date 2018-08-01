<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="rgpd")
 * @ORM\Entity(repositoryClass="App\Repository\RgpdRepository")
 */

class Rgpd
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="text")
     */
    private $rgpdHtml;

    /**
    * @ORM\Column(type="string", length=50)
    * @Assert\NotBlank(message="NAME")
    */
    private $name;
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getRgpdHtml()
    {
        return $this->rgpdHtml;
    }

    public function setRgpdHtml($rgpdHtml)
    {
        $this->rgpdHtml = $rgpdHtml;
    }
}