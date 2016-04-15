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
    public function listAction()
    {
        $coworkers = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findAllEnabledSortedByPosition();

        return $this->render(
            ':Frontend/Coworker:list.html.twig',
            [ 'coworkers' => $coworkers ]
        );
    }

    /**
     * @Route("/coworker/{id}", name="front_coworker_detail")
     *
     * @param $id
     * @return Response
     */
    public function detailAction($id)
    {
        $coworker = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findOneBy(
            array(
                'id' => $id,
            )
        );

        return $this->render(
            ':Frontend/Coworker:detail.html.twig',
            [ 'coworker' => $coworker ]
        );
    }
}
