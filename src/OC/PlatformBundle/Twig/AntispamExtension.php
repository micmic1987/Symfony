<?php

namespace OC\PlatformBundle\Twig;

use OC\PlatformBundle\Services\OCAntispam;

class AntispamExtension extends \Twig_Extension
{
  private $ocAntispam;

  public function __construct(OCAntispam $ocAntispam)
  {
    $this->ocAntispam = $ocAntispam;
  }

  public function checkIfArgumentIsSpam($text)
  {
    return $this->ocAntispam->isSpam($text);
  }

  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
      new \Twig_SimpleFunction('checkIfSpam2', array($this->ocAntispam, 'isSpam')),
    );
  }

  public function getName()
  {
    return 'OCAntispam';
  }
}