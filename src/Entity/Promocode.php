<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="promocode")
 * @ORM\Entity(repositoryClass="App\Repository\PromocodeRepository")
 */

class Promocode
{
    const TYPE_VALUE = 'value';
    const TYPE_PERCENT = 'percent';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string",  name="code", length=100)
     * @Assert\NotBlank(message="Código")
     */
    private $code;
    /** @ORM\Column(type="boolean", name="is_active", options={"default":0}) */
    private $isActive;

    /** @ORM\ManyToOne(targetEntity="Category") */
    private $category;
    /** @ORM\Column(name="start", type="datetime")
    * @Assert\NotBlank(message="Inicio *")
    */
    private $start;
    /** @ORM\Column(name="end", type="datetime")
    * @Assert\NotBlank(message="Fim *")
    */
    private $end;
    /**
    * @ORM\Column(type="integer", name="discount", length=3)
    * @Assert\NotBlank(message="Desconto *")
    */
    private $discount;

    /**
    * @ORM\Column(type="string", name="discount_type", columnDefinition="ENUM('value', 'percent')" )
    * @Assert\Choice({"value", "percent"})
    */
    private $discount_type = self::TYPE_PERCENT;
    

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

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = str_replace("'","’", $code);
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category) {
        $this->category = $category;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end)
    {
        $this->end = $end;
    }
    
    public function getDiscountType()
    {
        return $this->discount_type;
    }

    public function setDiscountType($discount_type)
    {
        if (!in_array($discount_type, array(self::TYPE_VALUE, self::TYPE_PERCENT))) {
            throw new \InvalidArgumentException("Invalid Discount Type");
        }
        $this->discount_type = $discount_type;
    }
}