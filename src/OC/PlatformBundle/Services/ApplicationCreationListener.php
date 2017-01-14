<?php

namespace OC\PlatformBundle\Services;

use OC\PlatformBundle\Entity\Application;;
use OC\PlatformBundle\Services\ApplicationMailer;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class ApplicationCreationListener
{
  private $applicationMailer;

  public function __construct(ApplicationMailer $applicationMailer)
  {
    $this->applicationMailer = $applicationMailer;
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();
    if(!$entity instanceof Application) {
      return;
    }
    try {
      $this->applicationMailer->sendNewNotificaiton($entity);
    }
    catch(\Swift_RfcComplianceException $e) {
      echo $e->getMessage();
    }
  }
}