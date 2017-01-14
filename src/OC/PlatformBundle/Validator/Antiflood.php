<?php

namespace OC\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
  public $message = "Votre message \"%string%\" est considéré comme flood";

  /**
   * Validation via service (cf. services.yml)
   * {@inheritDoc}
   * @see \Symfony\Component\Validator\Constraint::validatedBy()
   */
  public function validatedBy()
  {
    return 'oc_platform_antiflood';
  }
}