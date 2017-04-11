<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Coworker;
use AppBundle\Entity\SocialNetwork;
use AppBundle\Form\Type\CoworkerDataFormType;
use AppBundle\Service\NotificationService;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param Request  $request
     * @param Coworker $token
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function registerAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $coworker = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findOneBy(
            array(
                'token' => $token,
            )
        );

        if (!$coworker) {
            throw new EntityNotFoundException();
        }

        $originalSocialnetworks = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($coworker->getSocialNetworks() as $socialNetwork) {
            $originalSocialnetworks->add($socialNetwork);
        }

        $form = $this->createForm(CoworkerDataFormType::class, $coworker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist register data into DB
            $em = $this->getDoctrine()->getManager();
//            $em->persist($coworker);
//            $em->flush();
            // Send email notificationss
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            $messenger->sendCoworkerDataFormAdminNotification($coworker);

            // remove the relationship between the tag and the Task
            /** @var SocialNetwork $socialNetwork */
            foreach ($originalSocialnetworks as $socialNetwork) {
                if (false === $coworker->getSocialNetworks()->contains($socialNetwork)) {
                    // remove the Task from the Tag
//                    $tag->getTasks()->removeElement($task);

                    // if it was a many-to-one relationship, remove the relationship like this
                    $socialNetwork->setCoworker(null);

                    $em->persist($socialNetwork);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($socialNetwork);
                }
            }

            $em->persist($coworker);
            $em->flush();
//
            // redirect back to some edit page
//            return $this->redirectToRoute('task_edit', array('id' => $id));
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

        $originalSocialNetworks = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($coworker->getSocialNetworks() as $socialNetwork) {
            $originalSocialNetworks->add($socialNetwork);
        }

        $form = $this->createForm(CoworkerDataFormType::class, $coworker);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // remove the relationship between the tag and the Task
            /** @var SocialNetwork $socialNetwork */
            foreach ($originalSocialNetworks as $socialNetwork) {
                if (false === $coworker->getSocialNetworks()->contains($socialNetwork)) {
                    // remove the Task from the Tag
                    $socialNetwork->setCoworker(null)->removeElement($coworker);

                    // if it was a many-to-one relationship, remove the relationship like this
//                    $socialNetwork->setCoworker(null);

                    $em->persist($socialNetwork);

                    // if you wanted to delete the Tag entirely, you can also do that
//                     $em->remove($socialNetwork);
                }
            }

            $em->persist($coworker);
            $em->flush();

            // redirect back to some edit page
//            return $this->redirectToRoute('task_edit', array('id' => $id));
        }

        return $this->render(
            ':Frontend/Coworker:register_form.html.twig', array(
                'coworker' => $coworker,
                'form' => $form->createView(),
            )
        );
    }
}
