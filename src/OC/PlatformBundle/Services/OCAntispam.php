<?php

namespace OC\PlatformBundle\Services;

class OCAntispam
{
  private $mailer;
  private $locale;
  private $minLength;

  public function __construct(\Swift_Mailer $mailer, $minLength)
  {
    $this->mailer = $mailer;
    $this->minLength = $minLength;

  }

  /**
   * calls locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }

  /**
   * Vérifie si le texte est un spam ou non
   *
   * @params string $text
   * @return bool
   */
  public function isSpam($text)
  {
    return strlen($text) < $this->minLength;
  }
}