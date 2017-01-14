<?php

namespace OC\PlatformBundle\Bigbrother;

use Symfony\Component\Security\Core\User\UserInterface;

class MessageNotificator
{
  protected $mailer;

  public function __construct($mailer)
  {
    $this->mailer = $mailer;
  }

  public function notifyByEmail($message, UserInterface $user)
  {
    $message = \Swift_Message::newInstance()
      ->setSubject("Nouveau message d'un utilisateur surveillé")
      ->setFrom('admin@votresite.com')
      ->setTo('mic_mic1987@hotmail.com')
      ->setBody("L'utilisateur surveillé '".$user->getUsername()."' a posté le message suivant :
        '".$message."'")
    ;
    $this->mailer->send($message);
  }
}