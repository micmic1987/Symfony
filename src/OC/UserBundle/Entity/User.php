<?php

namespace OC\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
#use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="oc_user")
 * @ORM\Entity(repositoryClass="OC\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Ajoute un rôle par défaut
     * @ORM\PrePersist
     */
    public function addDefaultRole()
    {
      $this->roles = array('ROLE_USER');
    }
}

