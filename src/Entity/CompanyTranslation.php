<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_translation")
 * @ORM\Entity(repositoryClass="App\Repository\CompanyTranslationRepository")
 */
class CompanyTranslation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
   /** @ORM\ManyToOne(targetEntity="Locales", inversedBy="company_translation") */
    private $locale;

    /** @ORM\ManyToOne(targetEntity="Company", inversedBy="company_translation") */
    private $company;
    /**
    * @ORM\Column(name="description", type="text", nullable=true)
    */
    private $description;

    public function __construct()
    {   
        $this->locale = new ArrayCollection();  
    }

    // other properties and methods
    public function getId(){
        return $this->id;
    }


    public function getLocale()
    {
        return $this->locale;
    }
/*
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
*/

    public function setLocale(ArrayCollection $locale) {
        $this->locale = $locale;
    }

    public function addLocale(Locales $locale)
    {
        $locale->setLocale($this);
        $this->locale->add($locale);
    }
    
    public function removeLocale(Locales $locale)
    {
        $this->locale->removeElement($locale);
    }
    


    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = str_replace("'","’",$description);
    }

    public function getCompany() {
        return $this->company;
    }

    public function setCompany(Company $company) {
        $this->company = $company;
    }   

}