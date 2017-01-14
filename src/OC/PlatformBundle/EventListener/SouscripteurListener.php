<?php

namespace OC\PlatformBundle\EventListener;

use OC\PlatformBundle\Event\PlatformEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Bigbrother\MessageNotificator;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use OC\PlatformBundle\Beta\BetaHTMLAdder;

class SouscripteurListener implements EventSubscriberInterface
{
  protected $notificator;
  protected $listUsers;
  protected $betaHTML;
  protected $endDate;

  static public function getSubscribedEvents()
  {
    return array(
      PlatformEvents::POST_MESSAGE => array('processMessage', 2),
      'kernel.response' => array('processbeta', 3),
    );
  }

  public function __construct(MessageNotificator $notificator, $listUsers, BetaHTMLAdder $betaHTML, $endDate)
  {
    $this->notificator = $notificator;
    $this->listUsers = $listUsers;
    $this->betaHTML = $betaHTML;
    $this->endDate = new \DateTime($endDate);
  }

  public function processMessage(MessagePostEvent $event)
  {
    if(in_array($event->getUser()->getUsername(), $this->listUsers)) {
      $this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
    }
  }

  public function processBeta(FilterResponseEvent $event)
  {
    if(!$event->isMasterRequest()) {
      return;
    }

    $remainingDays = $this->endDate->diff(new \Datetime())->days;
    if($this->endDate->diff(new \Datetime())->invert == 0) {
      return;
    }

    $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);

    $event->setResponse($response);
  }
}