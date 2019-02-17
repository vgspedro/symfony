<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client implements UserInterface , \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;
    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $telephone;
    /**
     * @ORM\Column(type="array")
     */
    private $roles;
    /**
    * @ORM\Column(name="rgpd", type="boolean", length=1)
    */
    private $rgpd;
    /** 
     * @Assert\NotBlank()
     * @Assert\Type("Locales")
     *@ORM\ManyToOne(targetEntity="Locales", inversedBy="client") */
    private $locale;
    /**
    * @ORM\Column(name="cvv", type="text", nullable=true)
    */
    private $cvv;
    /**
    * @ORM\Column(name="card_name", type="text", nullable=true)
    */
    private $cardName;
    /**
    * @ORM\Column(name="card_nr", type="text", nullable=true)
    */
    private $cardNr;
    /**
    * @ORM\Column(name="card_date", type="text", nullable=true)
    */
    private $cardDate;

    public function __construct($email = '', $password = '', $salt = '') {
        $this->roles = array('ROLE_CLIENT');
        $this->email = $email;
        $this->password = $password;
    }

    // other properties and methods
    public function getId(){
        return $this->id;
    }


    public function getCvv()
    {
        return str_rot13(base64_decode($this->cvv));
    }

    public function setCvv($cvv)
    {
        $this->cvv = base64_encode(str_rot13($cvv));
    }

    public function getCardNr()
    {
        return str_rot13(base64_decode($this->cardNr));
    }

    public function setCardNr($cardNr)
    {
        $this->cardNr = base64_encode(str_rot13($cardNr));
    }

    public function getCardName()
    {
        return str_rot13(base64_decode($this->cardName));
    }

    public function setCardName($cardName)
    {
        $this->cardName = base64_encode(str_rot13($cardName));
    }

    public function getCardDate()
    {
        return str_rot13(base64_decode($this->cardDate));
    }

    public function setCardDate($cardDate)
    {
        $this->cardDate = base64_encode(str_rot13($cardDate));
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale(Locales $locale)
    {
        $this->locale = $locale;
    }

    public function getEmail()
    {
        return str_rot13(base64_decode($this->email));
    }


    public function setEmail($email)
    {
        $this->email = base64_encode(str_rot13($email));
    }

    public function getUsername()
    {
        return str_rot13(base64_decode($this->username));
    }

    public function setUsername($username)
    {
        $this->username = base64_encode(str_rot13($username));
    }

    public function getAddress()
    {
        return str_rot13(base64_decode($this->address));
    }

    public function setAddress($address)
    {
        $this->address = base64_encode(str_rot13($address));
    }

    public function getTelephone()
    {
        return str_rot13(base64_decode($this->telephone));
    }

    public function setTelephone($telephone)
    {
        $this->telephone = base64_encode(str_rot13($telephone));
    }

    public function getRgpd()
    {
        return $this->rgpd;
    }

    public function setRgpd($rgpd)
    {
        $this->rgpd = $rgpd;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
                $this->id,
                $this->email,
                $this->password
        ));
    }
    
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            ) = unserialize($serialized);
    }
}