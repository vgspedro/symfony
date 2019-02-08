<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocalesRepository")
 * @UniqueEntity(fields="name", message="Código Iso Existente")
 * @UniqueEntity(fields="filename", message="Nome Imagem Existente")
 */

class Locales
{  
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /** @ORM\Column(length=10, name="name")*/
    private $name;
    /** @ORM\Column(length=20, name="filename")*/
    private $filename;
    
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = str_replace("'","’",$name);
    }   

    public function getFilename() {
        return '/images/'.$this->filename;
    }

    public function setFilename($filename) {
        $this->filename = $filename;
    }  
}
