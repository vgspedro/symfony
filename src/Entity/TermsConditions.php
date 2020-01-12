<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="terms_conditions")
 * @ORM\Entity(repositoryClass="App\Repository\TermsConditionsRepository")
 */

class TermsConditions
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
    private $termsHtml;

    /**
    * @ORM\Column(type="text")
    * @Assert\NotBlank(message="Titulo *")
    */
    private $name;
    /**
     *@ORM\ManyToOne(targetEntity="Locales", inversedBy="terms_conditions") */
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

    public function getTermsHtml()
    {
        return $this->termsHtml;
    }

    public function setTermsHtml($termsHtml)
    {
        $this->termsHtml = $termsHtml;
    }
}