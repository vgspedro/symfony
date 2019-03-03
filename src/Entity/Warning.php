<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="warning")
 * @ORM\Entity(repositoryClass="App\Repository\WarningRepository")
 */

class Warning
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=250)
     */
    private $infoPt;
    /**
     * @ORM\Column(type="string", length=250)
     */
    private $infoEn;

    /**
    * @ORM\Column(type="boolean", name="visible", options={"default":0})
     */
    private $visible;

    public function getId()
    {
        return $this->id;
    }

    public function getInfoPt()
    {
        return $this->infoPt;
    }

    public function setInfoPt($infoPt)
    {
        $this->infoPt = str_replace("'","’",$infoPt);
    }

    public function getInfoEn()
    {
        return $this->infoEn;
    }

    public function setInfoEn($infoEn)
    {
        $this->infoEn = str_replace("'","’",$infoEn);
    }


    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
    }


}