<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Coworker;
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
        $coworkers = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findAllEnabledSortedBySurname();
        /** @var Coworker $coworker */
        foreach ($coworkers as $coworker) {
            $socialNetworks = $this->getDoctrine()->getRepository('AppBundle:SocialNetwork')->getCoworkerSocialNetworksSortedByTitle($coworker);
            $coworker->setSocialNetworks($socialNetworks);
        }

        return $this->render(
            ':Frontend/Coworker:list.html.twig',
            ['coworkers' => $coworkers]
        );
    }

    /**
     * @Route("/coworker/{slug}", name="front_coworker_detail")
     *
     * @param $slug
     *
     * @return Response
     */
    public function detailAction($slug)
    {
        $coworker = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findOneBy(
            array(
                'slug' => $slug,
            )
        );
        $socialNetworks = $this->getDoctrine()->getRepository('AppBundle:SocialNetwork')->getCoworkerSocialNetworksSortedByTitle($coworker);
        $coworker->setSocialNetworks($socialNetworks);

        return $this->render(
            ':Frontend/Coworker:detail.html.twig', array(
                'coworker' => $coworker,
            )
        );
    }

    /**
     * @Route("/registre/{token}", name="front_coworker_register")
     *
     * @param $token
     *
     * @return Response
     */
    public function registerAction($token)
    {
        $coworker = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findOneBy(
            array(
                'slug' => $slug,
            )
        );

        return $this->render(
            ':Frontend/Coworker:detail.html.twig', array(
                'coworker' => $coworker,
            )
        );
    }
}
