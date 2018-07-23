<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\Type\ContactNewsletterType;
use AppBundle\Manager\MailchimpManager;
use AppBundle\Service\NotificationService;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogController
 *
 * @category Controller
 */
class BlogController extends Controller
{
    /**
     * @Route("/blog/{pagina}", name="front_blog")
     *
     * @param Request $request
     * @param int $pagina
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function indexAction(Request $request, $pagina = 1)
    {
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->getAllEnabledSortedByTitle();
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getAllEnabledSortedByPublishedDateWithJoinUntilNow();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($posts, $pagina, 9);

        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->remove('recaptcha');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            $form->remove('recaptcha');
        }

        return $this->render(':Frontend:Blog/list.html.twig',
            [
                'pagination' => $pagination,
                'tags' => $tags,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/blog/{year}/{month}/{day}/{slug}", name="front_blog_detail")
     *
     * @param Request $request
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $slug
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function postDetailAction(Request $request, $year, $month, $day, $slug)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $year.'-'.$month.'-'.$day);

        /** @var Post $post */
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->findOneBy(
            array(
                'publishedAt' => $date,
                'slug' => $slug,
            )
        );

        if (!$post) {
            throw new EntityNotFoundException();
        }

        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->remove('recaptcha');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            $form->remove('recaptcha');
        }

        return $this->render('Frontend/Blog/detail.html.twig',
            [
                'post' => $post,
                'tags' => $this->getDoctrine()->getRepository('AppBundle:Tag')->getAllEnabledSortedByTitle(),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/blog/categoria/{slug}/{pagina}", name="front_blog_tag_detail")
     *
     * @param Request $request
     * @param string $slug
     * @param int $pagina
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function tagDetailAction(Request $request, $slug, $pagina = 1)
    {
        $tags = $this->getDoctrine()->getRepository('AppBundle:Tag')->getAllEnabledSortedByTitle();
        /** @var Tag $tag */
        $tag = $this->getDoctrine()->getRepository('AppBundle:Tag')->findOneBy(
            array(
                'slug' => $slug,
            )
        );

        if (!$tag) {
            throw new EntityNotFoundException();
        }
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->getPostsByTagEnabledSortedByPublishedDate($tag);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($posts, $pagina, 9);

        $contact = new ContactMessage();
        $form = $this->createForm(ContactNewsletterType::class, $contact);
        $form->remove('recaptcha');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setFlashMailchimpSubscribeAndEmailNotifications($contact);
            // Clean up new form
            $form = $this->createForm(ContactNewsletterType::class);
            $form->remove('recaptcha');
        }

        return $this->render(':Frontend/Blog:tag_detail.html.twig',
            [
                'tags' => $tags,
                'tag' => $tag,
                'pagination' => $pagination,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param ContactMessage $contact
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function setFlashMailchimpSubscribeAndEmailNotifications($contact)
    {
        /** @var MailchimpManager $mailchimpManager */
        $mailchimpManager = $this->get('app.mailchimp_manager');
        /** @var NotificationService $messenger */
        $messenger = $this->get('app.notification');
        // Subscribe contact to free-trial mailchimp list
        $userSubscriptionResult = $mailchimpManager->subscribeContactToList($contact, $this->getParameter('mailchimp_newsletter_list_id'));
        // Send email notifications
        $adminDeliveryResult = $messenger->sendNewsletterSubscriptionAdminNotification($contact, 'activitats');
        // Set frontend flash message
        if ($userSubscriptionResult === true && $adminDeliveryResult > 0) {
            $this->addFlash(
                'notice',
                'Gràcies per registrar-te al newsletter.'
            );
        } else {
            $this->addFlash(
                'danger',
                'Ho sentim, s\'ha produït un error a durant el procés de registre al newsletter. Torna a intentar-ho.'
            );
        }
    }
}
