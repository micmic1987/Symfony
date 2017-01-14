<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccueilController extends Controller
{
    public function indexAction()
    {
        return $this->render('OCCoreBundle:Accueil:index.html.twig');
    }

    public function contactAction(Request $request)
    {
      $request->getSession()->getFlashBag()->add('notice', 'Message flash : La page de contact n\'est pas encore disponible, merci de revenir plus tard.');
      $request->getSession()->getFlashBag()->add('notice', 'Message 2');
      return $this->render('OCCoreBundle:Accueil:contact.html.twig');
    }
}
