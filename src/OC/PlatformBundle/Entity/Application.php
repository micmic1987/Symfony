<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Application
 *
 * @ORM\Table(name="oc_application")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ApplicationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Application
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\NotBlank(message="Champ obligatoire")
     * @Assert\Length(min=10, minMessage="L'auteur est trop court, il doit faire au moins {{ limit }} caractères")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank(message="Champ obligatoire")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(message="Adresse mail non valide")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="OC\PlatformBundle\Entity\Advert", inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    /**
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;


    /**
     * Constructeur
     */
    public function __construct()
    {
      $this->date = new \Datetime();
    }

    /**
     * @ORM\PrePersist
     */
    public function insertApplication()
    {
      $this->getAdvert()->incrementNbApplications();
    }

    /**
     * @ORM\PreRemove
     */
    public function deleteApplication()
    {
      $this->getAdvert()->decrementNbApplications();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Application
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Application
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Application
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set advert
     *
     * @param \OC\PlatformBundle\Entity\Advert $advert
     *
     * @return Application
     */
    public function setAdvert(\OC\PlatformBundle\Entity\Advert $advert)
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * Get advert
     *
     * @return \OC\PlatformBundle\Entity\Advert
     */
    public function getAdvert()
    {
        return $this->advert;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Application
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Application
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }
}
