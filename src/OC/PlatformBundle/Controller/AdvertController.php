<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
#use Symfony\Component\HttpFoundation\JsonResponse;
#use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
#use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
#use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;
use OC\PlatformBundle\Form\ApplicationType;

class AdvertController extends Controller
{
  public function indexAction($page, Request $request)
  {
    if($page === '') {
      $page = 1;
    }
    if($page < 1) {
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }
    $em = $this->getDoctrine()->getManager();
    $nbPerPage = 3;
    $listAdverts = $em
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage);

    $nbPages = ceil(count($listAdverts)/$nbPerPage);
    if($nbPages == 0) {
      $nbPages = 1;
    }
    if($page > $nbPages) {
      //throw new NotAcceptableHttpException('La page demandée n\'existe pas.');
      $request->getSession()->getFlashBag()->add('info', 'La page '.$page.' n\'existe pas.');
      return $this->redirectToRoute('oc_platform_home');
    }

    // Et modifiez le 2nd argument pour injecter notre liste
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages' => $nbPages,
      'page' => $page
    ));
  }

  public function menuAction($limit)
  {
    $em = $this
    ->getDoctrine()
    ->getManager();
    $listAdverts = $em
    ->getRepository('OCPlatformBundle:Advert')
    ->findBy(
      array(),
      array('date' => 'desc'),
      $limit,
      0
      );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array('listAdverts' => $listAdverts));

  }

  /**
   * @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
   */
  public function viewAction(Advert $advert)
  {
    /*$em = $this
      ->getDoctrine()
      ->getManager();
    $advert = $em
      ->getRepository('OCPlatformBundle:Advert')
      ->find($id);
      //->getAdvertWithCategories(array('Graphisme','Intégration'));
      //->getAdvertWithAll($id);

    if(null == $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }*/

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert' => $advert
    ));
  }

  public function viewSlugAction($year, $slug, $_format)
  {
    //$this->container->get('mailer');
    //$this->get('mailer');
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->getAdvertWithAllDql($slug);

    return $this->render('OCPlatformBundle:Advert:viewSlug.html.twig', array(
      'advert' => $advert
    ));
    return new Response("On pourrait afficher l'annonce correspondant au slug '".$slug."', créée en ".$year." et au format ".$_format.".");
  }

  /**
   * #@Security("has_role('ROLE_AUTEUR')")
   * @param Request $request
   * @throws AccessDeniedException
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function addAction(Request $request)
  {
    if(!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
      //throw new AccessDeniedException('Accès limité aux auteurs.');
    }

    $advert = new Advert();
    $advert->setTitle('Titre par défault');
    $form = $this->createForm(AdvertType::class, $advert);

    if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $advert->setUser($this->getUser());
      $event = new MessagePostEvent($advert->getContent(), $advert->getUser());
      $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);
      $advert->setContent($event->getMessage());
      $advert->setIp($request->getClientIp());

      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirectToRoute('oc_platform_view', array('advert_id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:add.html.twig', [
      'form' => $form->createView()
    ]);
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
    $form = $this->createForm(AdvertEditType::class, $advert);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
      $em->persist($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
      return $this->redirectToRoute('oc_platform_view', ['advert_id' => $advert->getId()]);
    }
    return $this->render('OCPlatformBundle:Advert:edit.html.twig', [
      'advert' => $advert,
      'form' => $form->createView()
      ]);
  }

  public function deleteAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if(null == $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $form = $this->get('form.factory')->create();

    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
      $em->remove($advert);
      $em->flush();
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien supprimée.');
      return $this->redirectToRoute('oc_platform_home');
    }

    return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
      'form' => $form->createView(),
      'advert' => $advert
    ));
  }

  public function applicationAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('OC\PlatformBundle\Entity\Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $application = new Application();
    $form = $this->createForm(ApplicationType::class, $application);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()) {
      $application
        ->setAdvert($advert)
        ->setIp($request->getClientIp())
      ;
      $em->persist($application);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Candidature déposée.');
      return $this->redirectToRoute('oc_platform_view', array('advert_id' => $id));
    }

    return $this->render('OCPlatformBundle:Advert:application.html.twig', array(
      'advert' => $advert,
      'form' => $form->createView(),
    ));
  }

  public function purgeAction($days)
  {
    $purgerAdvert = $this->container->get('oc_platform.purger.advert');
    $purgerAdvert->purge($days);

    return $this->render('OCPlatformBundle:Advert:purge.html.twig');
    return new Response('OK');
  }

  public function purgeServiceAction()
  {
    $server = new \SoapServer('path/to/hello.wsdl');
    $server->setObject($this->get('oc_platform.purger.advert'));
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml; charset=ISO-8859-1');
    ob_start();
    $server->handle();
    $response->setContent(ob_get_clean());
    return $response;
  }

  public function testValidatorAction()
  {
    $advert = new Advert();
    $advert->setDate(new \Datetime());
    $advert->setTitle('abc');
    $advert->setAuthor('A');

    $validator = $this->get('validator');
    $listErrors = $validator->validate($advert);
    if(count($listErrors) > 0) {
      return new Response((string) $listErrors);
    } else {
      return new Response("L'annonce est valide!");
    }
  }

  public function translationAction($name)
  {
    return $this->render('OCPlatformBundle:Advert:translation.html.twig', array(
      'name' => $name
    ));
  }

  /**
   * @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
   * @ParamConverter("application", options={"mapping": {"application_id": "id"}})
   * @param Advert $advert
   * @param Application $application
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function testParamConverterAction(Advert $advert, Application $application)
  {
    return new Response("auteur:".$advert->getAuthor().", email:".$application->getEmail());
  }

  /**
   * @ParamConverter("date", options={"format": "Y-m-d"})
   * @param \Datetime $date
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function testDateParamConverterAction(\Datetime $date)
  {
    return new Response("date:".$date->format('d/m/Y'));
  }

  /**
   * @ParamConverter("json")
   * @param unknown $json
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function ParamConverterAction($json)
  {
    return new Response(print_r($json, true));
  }


}