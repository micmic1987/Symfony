<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
  protected $betaHTML;
  protected $endDate;

  public function __construct(BetaHTMLAdder $betaHTML, $endDate)
  {
    $this->betaHTML = $betaHTML;
    $this->endDate = new \Datetime($endDate);
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
    //$event->stopPropagation();
  }
}