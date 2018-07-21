<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactHomepageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceController
 *
 * @category Controller
 */
class ServiceController extends Controller
{
    /**
     * @Route("/serveis", name="front_services")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function servicesAction(Request $request)
    {
        $contact = new ContactMessage();
        $form = $this->createForm(ContactHomepageType::class, $contact);
        $form->handleRequest($request);

        return $this->render(':Frontend:Service/services.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
