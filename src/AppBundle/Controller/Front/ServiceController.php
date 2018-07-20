<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceController
 */
class ServiceController extends Controller
{
    /**
     * @Route("/serveis", name="front_services")
     *
     * @return Response
     */
    public function servicesAction()
    {
        return $this->render(':Frontend:Service/services.html.twig');
    }
}
