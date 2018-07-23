<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\ContactMessage;
use AppBundle\Form\Type\ContactHomepageType;
use AppBundle\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceController
 *
 * @category Controller
 */
class ServiceController extends Controller
{
    /**
     * @Route("/serveis", name="front_services")
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function servicesAction(Request $request)
    {
        $contact = new ContactMessage();
        $form = $this->createForm(ContactHomepageType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var NotificationService $messenger */
            $messenger = $this->get('app.notification');
            // Send email notifications
            $userDeliveryResult = $messenger->sendCommonUserNotification($contact);
            $adminDeliveryResult = $messenger->sendCommonContactAdminNotification($contact, 'serveis');
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

        return $this->render(':Frontend:Service/services.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
