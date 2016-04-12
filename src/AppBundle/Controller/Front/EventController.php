<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    /**
     * @Route("/events", name="front_events_list")
     *
     * @return Response
     */
    public function listAction()
    {
        $events = $this->getDoctrine()->getRepository('AppBundle:Event')->findAll();

        return $this->render(
            ':Frontend/Event:list.html.twig',
            [ 'events' => $events ]
        );
    }

    /**
     * @Route("/event/{id}", name="front_event_detail")
     *
     * @param $id
     * @return Response
     */
    public function detailAction($id)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')->findOneBy(
            array(
                'id' => $id,
            )
        );

        return $this->render(
            ':Frontend/Event:detail.html.twig',
            [ 'event' => $event ]
        );
    }
}
