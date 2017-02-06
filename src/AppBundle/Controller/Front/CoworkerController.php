<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Coworker;
use AppBundle\Form\Type\CoworkerDataFormType;
use AppBundle\Service\NotificationService;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param $token
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function registerAction(Request $request, $token)
    {
        $coworker = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findOneBy(
            array(
                'token' => $token,
            )
        );

        if (!$coworker) {
            throw new EntityNotFoundException();
        }

        $form = $this->createForm(CoworkerDataFormType::class, $coworker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist register data into DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($coworker);
            $em->flush();
            // Send email notifications
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            $messenger->sendCoworkerDataFormAdminNotification($coworker);
            // Flash message
//            if ($messenger->sendCoworkerDataFormAdminNotification($coworker) != 0) {
//                $this->addFlash(
//                    'notice',
//                    'El teu missatge s\'ha enviat correctament'
//                );
//            } else {
//                $this->addFlash(
//                    'danger',
//                    'El teu missatge no s\'ha enviat'
//                );
//            }
        }

        return $this->render(
            ':Frontend/Coworker:register_form.html.twig', array(
                'coworker' => $coworker,
                'form' => $form->createView(),
            )
        );
    }
}
