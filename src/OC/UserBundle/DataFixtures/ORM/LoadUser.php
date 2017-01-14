<?php

namespace OC\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $listNames = array('Alexandre', 'Marine', 'Anna');
    foreach($listNames as $name) {
      $user = new User;
      $user
        ->setUsername($name)
        ->setPassword($name)
        ->setSalt('')
        ->setRoles(array('ROLE_USER'))
      ;
      $manager->persist($user);
    }
    $manager->flush();
  }
}