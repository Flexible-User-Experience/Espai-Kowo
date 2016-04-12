<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="front_homepage")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render(':Frontend:homepage.html.twig', array());
    }
}
