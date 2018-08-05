<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
*/
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;
    /** 
     * @ORM\OneToOne(targetEntity="Category", inversedBy="image")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;    
    /** @ORM\Column(length=100) */
    private $filename;
    
	public function getId() {
		return $this->id;
	}

	public function getFilename() {
		return $this->filename;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}
	
	public function getCategory() {
		return $this->category;
	}

	public function setCategory(Category $category) {
		$this->category = $category;
	}    
}
