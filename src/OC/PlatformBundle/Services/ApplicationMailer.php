<?php

namespace OC\PlatformBundle\Services;

use OC\PlatformBundle\Entity\Application;

class ApplicationMailer
{
  private $mailer;

  public function __construct(\Swift_Mailer $mailer)
  {
    $this->mailer = $mailer;
  }
  public function sendNewNotificaiton(Application $application)
  {
    $message = new \Swift_Message(
      'Nouvelle candidature',
      'Vous avez reçu une nouvelle candidature.'
    );
    $message->addTo($application->getEmail())
      ->addFrom('admin@votresite.com');
    $this->mailer->send($message);
  }
}