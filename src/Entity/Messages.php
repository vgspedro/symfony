<?php
/**
 * Created Roman Belousov
 * Date: 07.04.16
 */

namespace belousovr\belousovChatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use FOS\UserBundle\Model\User;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="belousovr\belousovChatBundle\Entity\MessagesRepository")
 * @ORM\Table(name="belousovr_messages")
 */
class Messages
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="messageText", type="string", length=255)
     */
    protected $messageText;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reading", type="boolean")
     */
    protected $reading;


    protected $author;

    protected $addressee;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set messageText
     *
     * @param string $messageText
     *
     * @return Messages
     */
    public function setMessageText($messageText)
    {
        $this->messageText = $messageText;

        return $this;
    }

    /**
     * Get messageText
     *
     * @return string
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * Set reading
     *
     * @param boolean $reading
     *
     * @return Messages
     */
    public function setReading($reading = false)
    {
        $this->reading = $reading;

        return $this;
    }

    /**
     * Get reading
     *
     * @return boolean
     */
    public function getReading()
    {
        return $this->reading;
    }

    /**
     * Set author
     *
     * @param $author
     *
     * @return Messages
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param $addressee
     *
     * @return Messages
     */
    public function setAddressee($addressee)
    {
        $this->addressee = $addressee;

        return $this;
    }

    /**
     * Get author
     */
    public function getAddressee()
    {
        return $this->addressee;
    }
}
