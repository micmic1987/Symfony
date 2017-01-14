<?php

 namespace OC\PlatformBundle\Bigbrother;

 use OC\PlatformBundle\Event\MessagePostEvent;

 class MessageListener
 {
   protected $notificator;
   protected $listUsers = array();

   public function __construct(MessageNotificator $notificator, $listUsers)
   {
     $this->notificator = $notificator;
     $this->listUsers = $listUsers;
   }

   public function processMessage(MessagePostEvent $event)
   {
     if(in_array($event->getUser()->getUsername(), $this->listUsers)) {
       $this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
     }
   }
 }