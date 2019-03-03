<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="gallery")
 * @ORM\Entity(repositoryClass="App\Repository\GalleryRepository")
 */

class Gallery
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="NAME_PT")
     */
    private $namePt;
    /**
     * @Assert\NotBlank(message="NAME_EN")
     * @ORM\Column(type="string", length=50)
     */
    private $nameEn;

    /** @ORM\Column(type="boolean", name="is_active", options={"default":0}) */
    private $isActive;
    /**
     * @ORM\Column(type="string", name="image")
     * @Assert\File(mimeTypes={"image/gif", "image/png", "image/jpeg"})
     */
    private $image;

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNamePt()
    {
        return $this->namePt;
    }

    public function setNamePt($namePt)
    {
        $this->namePt = str_replace("'","’", $namePt);
    }

    public function getNameEn()
    {
        return $this->nameEn;
    }

    public function setNameEn($nameEn)
    {
        $this->nameEn = str_replace("'","’",$nameEn);
    }
}