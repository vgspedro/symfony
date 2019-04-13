<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="about_us")
 * @ORM\Entity(repositoryClass="App\Repository\AboutUsRepository")
 */

class AboutUs
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
    * @Assert\NotBlank(message="Titulo *")
    */
    private $name;
    /**
     *@ORM\ManyToOne(targetEntity="Locales") */
    private $locales;
    
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

    public function getLocales()
    {
        return $this->locales;
    }

    public function setLocales(Locales $locales)
    {
        $this->locales = $locales;
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