<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Entity\Coworker;
use AppBundle\Entity\SocialNetwork;
use AppBundle\Form\Type\ContactHomepageType;
use AppBundle\Form\Type\CoworkerDataFormType;
use AppBundle\Service\NotificationService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CoworkerController
 *
 * @category Controller
 */
class CoworkerController extends Controller
{
    /**
     * @Route("/coworkers", name="front_coworkers_list")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $coworkers = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findAllEnabledSortedBySurname();
        /** @var Coworker $coworker */
        foreach ($coworkers as $coworker) {
            $socialNetworks = $this->getDoctrine()->getRepository('AppBundle:SocialNetwork')->getCoworkerSocialNetworksSortedByTitle($coworker);
            $coworker->setSocialNetworks($socialNetworks);
        }

        $contact = new ContactMessage();
        $form = $this->createForm(ContactHomepageType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            // Send email notifications
            $userDeliveryResult = $messenger->sendCommonUserNotification($contact);
            $adminDeliveryResult = $messenger->sendCommonContactAdminNotification($contact, 'coworkers');
            // Set frontend flash message
            if ($userDeliveryResult > 0 && $adminDeliveryResult > 0) {
                $this->addFlash(
                    'notice',
                    'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Ho sentim, s\'ha produït un error a l\'enviar el missatge de contacte. Torna a intentar-ho.'
                );
            }
            // Clean up new form
            $form = $this->createForm(ContactHomepageType::class);
        }

        return $this->render(
            ':Frontend/Coworker:list.html.twig',
            [
                'coworkers' => $coworkers,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/coworker/{slug}", name="front_coworker_detail")
     *
     * @param Request $request
     * @param string  $slug
     *
     * @return Response
     */
    public function detailAction(Request $request, $slug)
    {
        $coworker = $this->getDoctrine()->getRepository('AppBundle:Coworker')->findOneBy(
            array(
                'slug' => $slug,
            )
        );
        $socialNetworks = $this->getDoctrine()->getRepository('AppBundle:SocialNetwork')->getCoworkerSocialNetworksSortedByTitle($coworker);
        $coworker->setSocialNetworks($socialNetworks);

        $contact = new ContactMessage();
        $form = $this->createForm(ContactHomepageType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            // Send email notifications
            $userDeliveryResult = $messenger->sendCommonUserNotification($contact);
            $adminDeliveryResult = $messenger->sendCommonContactAdminNotification($contact, 'coworker');
            // Set frontend flash message
            if ($userDeliveryResult > 0 && $adminDeliveryResult > 0) {
                $this->addFlash(
                    'notice',
                    'Ens posarem en contacte amb tu el més aviat possible. Gràcies.'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Ho sentim, s\'ha produït un error a l\'enviar el missatge de contacte. Torna a intentar-ho.'
                );
            }
            // Clean up new form
            $form = $this->createForm(ContactHomepageType::class);
        }

        return $this->render(
            ':Frontend/Coworker:detail.html.twig', array(
                'coworker' => $coworker,
                'form' => $form->createView(),
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
            $em->flush();
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

            if ($this->get('kernel')->getEnvironment() == 'prod') {
                $coworker->setToken(null);
            }
            $em->flush();

            $this->addFlash(
                'notice',
                'Les teves dades s\'han registrat correctament'
            );

            return $this->redirectToRoute('front_homepage');
        }

        return $this->render(
            ':Frontend/Coworker:register_form.html.twig', array(
                'coworker' => $coworker,
                'form' => $form->createView(),
            )
        );
    }
}
