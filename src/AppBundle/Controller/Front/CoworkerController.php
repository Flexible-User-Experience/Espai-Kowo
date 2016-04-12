<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CoworkerController extends Controller
{
    /**
     * @Route("/coworkers", name="front_coworkers_list")
     *
     * @return Response
     */
    public function indexAction()
    {
        $coworkers = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findAll();

        return $this->render(
            ':Frontend/Coworker:list.html.twig',
            [ 'coworkers' => $coworkers ]
        );
    }
}
