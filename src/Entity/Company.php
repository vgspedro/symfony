<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */

class Company
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;   
    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="NAME")
     */
    private $name;

    /**
    * @Assert\NotBlank(message="ADDRESS")
    * @ORM\Column(type="string", length=50, name="address")
    */
    private $address;
    /**
    * @Assert\NotBlank(message="P_CODE")
    * @ORM\Column(type="string", length=10, name="p_code")
    */
    private $p_code;
    /**
    * @Assert\NotBlank(message="CITY")
    * @ORM\Column(type="string", length=10, name="city")
    */
    private $city;
    /**
    *@ORM\ManyToOne(targetEntity="Countries") */
    private $country;
    /**
    * @Assert\NotBlank(message="EMAIL")
    * @ORM\Column(type="string", length=30, name="email")
    */
    private $email;
    /**
    * @Assert\NotBlank(message="EMAIL")
    * @ORM\Column(type="string", length=128, name="email_pass")
    */
    private $email_pass;
    /**
    * @ORM\Column(type="integer")
    */
    private $email_port;
    /**
    * @ORM\Column(type="string", length=50, name="email_smtp")
    */
    private $email_smtp;
    /**
    * @ORM\Column(type="string", length=4, name="email_certificade")
    */
    private $email_certificade;
    /**
     * @ORM\Column(type="string", length=20, name="telephone", nullable=true)
     */
    private $telephone;
    /**
    * @ORM\Column(type="text", name="meta_keywords", nullable=true)
    */
    private $meta_keywords;
        /**
     * @ORM\Column(type="text", name="meta_description", nullable=true)
     */
    private $meta_description;
    /**
     *@ORM\ManyToOne(targetEntity="Currency") */
    private $currency;
    /**
     * @ORM\Column(type="string", length=50, name="fiscal_number")
     * @Assert\NotBlank(message="FISCAL_NUMBER")
     */
    private $fiscal_number;
    /**
     * @ORM\Column(type="string", length=50, name="coords_google_maps")
     * @Assert\NotBlank(message="COORDS_GOOGLE_MAPS")
     */
    private $coords_google_maps;
    /**
     * @ORM\Column(type="string", length=50, name="google_maps_api_key")
     * @Assert\NotBlank(message="GOOGLE_MAPS_API_KEY")
     */
    private $google_maps_api_key;
    /**
     * @ORM\Column(type="string", name="logo", nullable=true)
     * @Assert\File(mimeTypes={"image/gif", "image/png", "image/jpeg"})
     */
    private $logo;

    /** @ORM\Column(type="string", name="link_my_domain",nullable=true)*/
    private $link_my_domain;
    /** @ORM\Column(type="string", name="link_facebook",nullable=true)*/
    private $link_facebook;
    /** @ORM\Column(type="string", name="link_twitter",nullable=true)*/
    private $link_twitter;
    /** @ORM\Column(type="string", name="link_instagram",nullable=true)*/
    private $link_instagram;
        /** @ORM\Column(type="string", name="link_linken",nullable=true)*/
    private $link_linken;
    /** @ORM\Column(type="string", name="link_pinterest",nullable=true)*/
    private $link_pinterest;
    /** @ORM\Column(type="string", name="link_youtube",nullable=true)*/
    private $link_youtube;
    /** @ORM\Column(type="string", name="link_behance",nullable=true)*/
    private $link_behance;
    /** @ORM\Column(type="string", name="link_snapchat",nullable=true)*/
    private $link_snapchat;

    /** @ORM\Column(type="boolean", name="link_facebook_active", options={"default":0}) */
    private $link_facebook_active = false;
       /** @ORM\Column(type="boolean", name="link_twitter_active", options={"default":0}) */
    private $link_twitter_active = false;
        /** @ORM\Column(type="boolean", name="link_instagram_active", options={"default":0}) */
    private $link_instagram_active = false;
    /** @ORM\Column(type="boolean", name="link_linken_active", options={"default":0}) */
    private $link_linken_active = false;
    /** @ORM\Column(type="boolean", name="link_pinterest_active", options={"default":0}) */
    private $link_pinterest_active = false;
    /** @ORM\Column(type="boolean", name="link_youtube_active",nullable=true)*/
    private $link_youtube_active = false;
    /** @ORM\Column(type="boolean", name="link_behance_active",nullable=true)*/
    private $link_behance_active = false;
    /** @ORM\Column(type="boolean", name="link_snapchat_active",nullable=true)*/
    private $link_snapchat_active = false;

    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }
    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;
    }

    public function getMetaDescription()
    {
        return $this->meta_description;
    }
    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;
    }

    public function getEmailPort()
    {
        return $this->email_port;
    }
    public function setEmailPort($email_port)
    {
        $this->email_port = $email_port;
    }

    public function getEmailSmtp()
    {
        return $this->email_smtp;
    }
    public function setEmailSmtp($email_smtp)
    {
        $this->email_smtp = $email_smtp;
    }

    public function getEmailCertificade()
    {
        return $this->email_certificade;
    }
    public function setEmailCertificade($email_certificade)
    {
        $this->email_certificade = $email_certificade;
    }


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
        $this->name = str_replace("'","’",$name);
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = str_replace("'","’",$address);
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
    }

    public function getPCode()
    {
        return $this->p_code;
    }

    public function setPCode($p_code)
    {
        $this->p_code = str_replace("'","’",$p_code);
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = str_replace("'","’",$city);
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(Countries $country)
    {
        $this->country = $country;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = str_replace("'","’",$email);
    }

    public function getEmailPass()
    {
        return $this->email_pass;
    }

    public function setEmailPass($email_pass)
    {
        $this->email_pass = $email_pass;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = str_replace("'","’",$telephone);
    }

    public function getFiscalNumber()
    {
        return $this->fiscal_number;
    }

    public function setFiscalNumber($fiscal_number)
    {
        $this->fiscal_number = str_replace("'","’",$fiscal_number);
    }

    public function getCoordsGoogleMaps()
    {
        return $this->coords_google_maps;
    }

    public function setCoordsGoogleMaps($coords_google_maps)
    {
        $this->coords_google_maps = str_replace("'","’",$coords_google_maps);
    }

    public function getGoogleMapsApiKey()
    {
        return $this->google_maps_api_key;
    }

    public function setGoogleMapsApiKey($google_maps_api_key)
    {
        $this->google_maps_api_key = str_replace("'","’",$google_maps_api_key);
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    public function getLinkMyDomain()
    {
        return $this->link_my_domain;
    }

    public function setLinkMyDomain($link_my_domain)
    {
        $this->link_my_domain = str_replace("'","’",$link_my_domain);
    }

    public function getLinkFacebook()
    {
        return $this->link_facebook;
    }

    public function setLinkFacebook($link_facebook)
    {
        $this->link_facebook = str_replace("'","’",$link_facebook);
    }

    public function getLinkTwitter()
    {
        return $this->link_twitter;
    }

    public function setLinkTwitter($link_twitter)
    {
        $this->link_twitter = str_replace("'","’",$link_twitter);
    }

    public function getLinkInstagram()
    {
        return $this->link_instagram;
    }

    public function setLinkInstagram($link_instagram)
    {
        $this->link_instagram = str_replace("'","’",$link_instagram);
    }

    public function getLinkLinken()
    {
        return $this->link_linken;
    }

    public function setLinkLinken($link_linken)
    {
        $this->link_linken = str_replace("'","’",$link_linken);
    }

    public function getLinkPinterest()
    {
        return $this->link_pinterest;
    }

    public function setLinkPinterest($link_pinterest)
    {
        $this->link_pinterest = str_replace("'","’",$link_pinterest);
    }

    public function getLinkBehance()
    {
        return $this->link_behance;
    }

    public function setLinkBehance($link_behance)
    {
        $this->link_behance = str_replace("'","’",$link_behance);
    }

    public function getLinkYouTube()
    {
        return $this->link_youtube;
    }

    public function setLinkYouTube($link_youtube)
    {
        $this->link_youtube = str_replace("'","’",$link_youtube);
    }

    public function getLinkSnapChat()
    {
        return $this->link_snapchat;
    }

    public function setLinkSnapChat($link_snapchat)
    {
        $this->link_snapchat = str_replace("'","’",$link_snapchat);
    }


    public function getLinkBehanceActive()
    {
        return $this->link_behance_active;
    }

    public function setLinkBehanceActive($link_behance_active)
    {
        $this->link_behance_active = $link_behance_active;
    }

    public function getLinkYouTubeActive()
    {
        return $this->link_youtube_active;
    }

    public function setLinkYouTubeActive($link_youtube_active)
    {
        $this->link_youtube_active = $link_youtube_active;
    }

    public function getLinkFacebookActive()
    {
        return $this->link_facebook_active;
    }

    public function setLinkFacebookActive($link_facebook_active)
    {
        $this->link_facebook_active = $link_facebook_active;
    }

    public function getLinkTwitterActive()
    {
        return $this->link_twitter_active;
    }

    public function setLinkTwitterActive($link_twitter_active)
    {
        $this->link_twitter_active =$link_twitter_active;
    }

    public function getLinkInstagramActive()
    {
        return $this->link_instagram_active;
    }

    public function setLinkInstagramActive($link_instagram_active)
    {
        $this->link_instagram_active = $link_instagram_active;
    }

    public function getLinkLinkenActive()
    {
        return $this->link_linken_active;
    }

    public function setLinkLinkenActive($link_linken_active)
    {
        $this->link_linken_active = $link_linken_active;
    }

    public function getLinkPinterestActive()
    {
        return $this->link_pinterest_active;
    }

    public function setLinkPinterestActive($link_pinterest_active)
    {
        $this->link_pinterest_active = $link_pinterest_active;
    }

    public function getLinkSnapChatActive()
    {
        return $this->link_snapchat_active;
    }

    public function setLinkSnapChatActive($link_snapchat_active)
    {
        $this->link_snapchat_active = $link_snapchat_active;
    }
}