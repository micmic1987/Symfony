<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use OC\PlatformBundle\Entity\Skill;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSkill implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');
    
    foreach($names as $name)
    {
      $skill = new Skill;
      $skill->setName($name);
      $manager->persist($skill);
    }
    $manager->flush();
  }
}