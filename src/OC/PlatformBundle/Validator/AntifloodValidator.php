<?php

namespace OC\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AntifloodValidator extends ConstraintValidator
{
  private $em;
  private $requestStack;

  public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
  {
    $this->em = $em;
    $this->requestStack = $requestStack;
  }

  public function validate($value, Constraint $constraint)
  {
    $request = $this->requestStack->getCurrentRequest();
    $ip = $request->getClientIp();

    $isFlood = $this->em
      ->getRepository('OCPlatformBundle:Advert')
      ->isFlood($ip, 15)
    ;

    if($isFlood) {
      //$this->context->addViolation($constraint->message);
      $this->context
        ->buildViolation($constraint->message)
        ->setParameters(array('%string%' => $value))
        ->addViolation()
      ;
    }
  }
}