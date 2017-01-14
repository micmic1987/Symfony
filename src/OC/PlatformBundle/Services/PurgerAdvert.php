<?php

namespace OC\PlatformBundle\Services;

use Doctrine\ORM\EntityManager;

class PurgerAdvert
{
  private $entityManager;

  public function __construct(EntityManager $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  public function purge($days)
  {
    $em = $this->entityManager;
    $date = new \Datetime();
    $date->sub(new \DateInterval('P'.$days.'D'));
    $listOldAdverts = $em->getRepository('OCPlatformBundle:Advert')->getOldAdverts($date);
    foreach($listOldAdverts as $advert) {
      $advertSkills = $advert->getAdvertSkills();
      foreach($advertSkills as $advertSkill) {
        $em->remove($advertSkill);
      }
      $em->remove($advert);
    }
    $em->flush();
  }
}