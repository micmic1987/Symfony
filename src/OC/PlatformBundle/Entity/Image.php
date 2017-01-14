<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Image
 *
 * @ORM\Table(name="oc_image")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Image
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    /**
     * File
     */
    private $file;

    /**
     * Old file name
     */
    private $tempFilename;


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
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Get file
     */
    public function getFile()
    {
      return $this->file;
    }

    /**
     * Set file
     */
    public function setFile(UploadedFile $file)
    {
      $this->file = $file;

      if(null !== $this->url) {
        $this->tempFilename = $this->url;
        $this->url = null;
        $this->alt = null;
      }
    }

    /**
     * Insert en bdd dans url et alt
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
      if(null === $this->file) {
        return;
      }
      $this->url = $this->file->guessExtension();
      $this->alt = $this->file->getClientOriginalName();
    }

    /**
     * Upload le fichier
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
      if(null === $this->file) {
        return;
      }

      if(null !== $this->tempFilename) {
        $oldFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
        if(file_exists($oldFilename)) {
          unlink($oldFilename);
        }
      }

      $this->file->move($this->getUploadRootDir(), $this->id.'.'.$this->url);
    }

    /**
     * Supprime de la bdd
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
      $this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->url;
    }

    /**
     * Supprime le fichier
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
      if(file_exists($this->tempFilename)) {
        unlink($this->tempFilename);
      }

    }

    /**
     * Chemin relatif
     */
    public function getUploadDir()
    {
      return 'uploads/img';
    }

    /**
     * Chemin absolu
     */
    public function getUploadRootDir()
    {
      return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get file url
     */
    public function getFileUrl()
    {
      return $this->getUploadDir().'/'.$this->id.'.'.$this->url;
    }
}
