<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Coworker;
use AppBundle\Service\NotificationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class CoworkerAdminController.
 *
 * @category Controller
 *
 * @author   David Romaní <david@flux.cat>
 */
class CoworkerAdminController extends BaseAdminController
{
    /**
     * Custom show action redirect to public frontend view.
     *
     * @param int|string|null $id
     * @param Request         $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function showAction($id = null, Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Coworker $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        return $this->redirectToRoute(
            'front_coworker_detail',
            array(
                'slug' => $object->getSlug(),
            )
        );
    }

    /**
     * Custom data action send email notification to fill the coworkers own data.
     *
     * @param int|string|null $id
     * @param Request         $request
     *
     * @return RedirectResponse
     *
     * @throws NotFoundHttpException     If the object does not exist
     * @throws AccessDeniedHttpException If access is not granted
     */
    public function dataAction($id = null, Request $request = null)
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Coworker $object */
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        /** @var NotificationService $messenger */
        $messenger = $this->get('app.notification');
        $messenger->sendCoworkerDataFormNotification($object);

        return $this->redirectToRoute('admin_app_coworker_list');
    }
}
